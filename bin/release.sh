#!/bin/bash

# Release Automation Script for Laravel Simple Datatable
#
# This script automates the release process including:
# - Pre-release checks (git status, tests)
# - Version bumping (major, minor, patch)
# - Changelog generation
# - Git tagging and pushing
#
# Usage:
#   ./bin/release.sh [major|minor|patch]
#
# Example:
#   ./bin/release.sh patch
#
# Requirements:
# - git
# - composer
# - php

set -e

# Configuration
COLOR_RED='\033[0;31m'
COLOR_GREEN='\033[0;32m'
COLOR_YELLOW='\033[1;33m'
COLOR_BLUE='\033[0;34m'
COLOR_RESET='\033[0m'

LOG_FILE="release.log"

# Logging function
log() {
    local level=$1
    local message=$2
    local timestamp=$(date "+%Y-%m-%d %H:%M:%S")
    local color=$COLOR_RESET

    case $level in
        INFO) color=$COLOR_BLUE ;;
        SUCCESS) color=$COLOR_GREEN ;;
        WARNING) color=$COLOR_YELLOW ;;
        ERROR) color=$COLOR_RED ;;
    esac

    echo -e "${color}[${level}] ${message}${COLOR_RESET}"
    echo "[${timestamp}] [${level}] ${message}" >> "$LOG_FILE"
}

# Error handling
handle_error() {
    log ERROR "An error occurred on line $1"
    exit 1
}

trap 'handle_error $LINENO' ERR

# Check for required commands
check_requirements() {
    log INFO "Checking system requirements..."
    
    # Check for git
    if ! command -v git &> /dev/null; then
        log ERROR "git is not installed or not in PATH."
        exit 1
    fi

    # Check for docker compose
    if command -v docker &> /dev/null && docker compose version &> /dev/null; then
        log INFO "Docker Compose detected."
    else
        # Fallback check for local php/composer if docker is not present
        for cmd in composer php; do
            if ! command -v $cmd &> /dev/null; then
                log ERROR "$cmd is not installed or not in PATH (and Docker Compose not found)."
                exit 1
            fi
        done
    fi
    
    log SUCCESS "All requirements met."
}

# Validate git status
check_git_status() {
    log INFO "Checking git status..."
    if [[ -n $(git status --porcelain) ]]; then
        log ERROR "Git working directory is dirty. Please commit or stash changes before releasing."
        exit 1
    fi
    
    # Check if we are on main branch
    current_branch=$(git branch --show-current)
    if [[ "$current_branch" != "main" ]]; then
        log WARNING "You are not on the main branch (current: $current_branch). Continue? (y/n)"
        read -r response
        if [[ "$response" != "y" ]]; then
            log INFO "Release aborted."
            exit 0
        fi
    fi
    log SUCCESS "Git status clean."
}

# Run tests
run_tests() {
    log INFO "Running test suite..."
    
    # Check if running in docker
    if [ -f "docker-compose.yml" ] && docker compose ps | grep -q "Up"; then
        log INFO "Detected Docker environment, running tests in container..."
        docker compose exec -T app vendor/bin/pest
    else
        if [ -f "vendor/bin/pest" ]; then
            vendor/bin/pest
        else
            log ERROR "Pest binary not found. Please run 'composer install'."
            exit 1
        fi
    fi
    
    log SUCCESS "Tests passed successfully."
}

# Get current version
get_current_version() {
    # Try to get latest tag, default to 0.0.0 if no tags
    latest_tag=$(git describe --tags --abbrev=0 2>/dev/null || echo "v0.0.0")
    echo "${latest_tag#v}"
}

# Bump version
bump_version() {
    local current_version=$1
    local bump_type=$2
    
    IFS='.' read -r major minor patch <<< "$current_version"
    
    case $bump_type in
        major)
            major=$((major + 1))
            minor=0
            patch=0
            ;;
        minor)
            minor=$((minor + 1))
            patch=0
            ;;
        patch)
            patch=$((patch + 1))
            ;;
        *)
            log ERROR "Invalid bump type: $bump_type. Usage: $0 [major|minor|patch]"
            exit 1
            ;;
    esac
    
    echo "$major.$minor.$patch"
}

# Update Changelog
update_changelog() {
    local new_version=$1
    local date=$(date "+%Y-%m-%d")
    
    log INFO "Updating CHANGELOG.md..."
    
    if [ ! -f "CHANGELOG.md" ]; then
        log WARNING "CHANGELOG.md not found. Creating..."
        echo "# Changelog" > CHANGELOG.md
    fi
    
    # Create a temporary file
    temp_file=$(mktemp)
    
    # Add header
    echo "# Changelog" > "$temp_file"
    echo "" >> "$temp_file"
    echo "All notable changes to \`laravel-simple-datatable\` will be documented in this file." >> "$temp_file"
    echo "" >> "$temp_file"
    
    # Add new version entry
    echo "## v$new_version - $date" >> "$temp_file"
    echo "" >> "$temp_file"
    echo "### Changed" >> "$temp_file"
    echo "- Maintenance release." >> "$temp_file"
    echo "" >> "$temp_file"
    
    # Append existing content skipping the first 4 lines (header)
    tail -n +5 CHANGELOG.md >> "$temp_file" 2>/dev/null || true
    
    mv "$temp_file" CHANGELOG.md
    
    log SUCCESS "CHANGELOG.md updated."
    log INFO "Please review CHANGELOG.md and add details if needed. Press Enter to continue..."
    read -r
}

# Main execution
main() {
    check_requirements
    
    # Parse arguments
    if [ -z "$1" ]; then
        log ERROR "Please provide a bump type (major, minor, patch)."
        echo "Usage: $0 [major|minor|patch]"
        exit 1
    fi
    
    BUMP_TYPE=$1
    
    check_git_status
    run_tests
    
    CURRENT_VERSION=$(get_current_version)
    NEW_VERSION=$(bump_version "$CURRENT_VERSION" "$BUMP_TYPE")
    
    log INFO "Current version: v$CURRENT_VERSION"
    log INFO "Target version:  v$NEW_VERSION"
    
    echo -e "${COLOR_YELLOW}Are you sure you want to release v$NEW_VERSION? (y/n)${COLOR_RESET}"
    read -r confirm
    if [[ "$confirm" != "y" ]]; then
        log INFO "Release aborted."
        exit 0
    fi
    
    update_changelog "$NEW_VERSION"
    
    # Commit changes
    log INFO "Committing changes..."
    git add CHANGELOG.md
    git commit -m "chore: release v$NEW_VERSION"
    
    # Create tag
    log INFO "Creating git tag v$NEW_VERSION..."
    git tag -a "v$NEW_VERSION" -m "Release v$NEW_VERSION"
    
    # Push
    log INFO "Pushing changes and tags to remote..."
    git push origin main
    git push origin "v$NEW_VERSION"
    
    log SUCCESS "Release v$NEW_VERSION completed successfully!"
    log INFO "Packagist will automatically update via GitHub Webhook."
}

# Start script
main "$@"
