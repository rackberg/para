#!/bin/sh

para --version 2>&1 > /dev/null
if [ ! $? -eq 0 ]; then
    echo "\033[0;34mPara is not installed\033[0m"
    exit
fi

CURRENT_DIR=$(pwd)
UPDATE_METHOD=$1

composer --version 2>&1 > /dev/null
COMPOSER_IS_AVAILABLE=$?

if [ ! $COMPOSER_IS_AVAILABLE -eq 0 ]; then
    echo "composer is not installed"
    exit
fi

git --version 2>&1 > /dev/null
GIT_IS_AVAILABLE=$?

if [ $GIT_IS_AVAILABLE -eq 0 ]; then
    cd ~/.para 2>&1 > /dev/null

    # Get the current installed tag
    currentTag=$(git describe --tags --always | cut -d\- -f1)

    if [ $UPDATE_METHOD == "stable" ]; then
        # Get new tags from the remote
        git fetch --tags --quiet

        # Get the latest tag name
        latestTag=$(git describe --tags `git rev-list --tags  --max-count=1`)

        if [ $currentTag == $latestTag ]; then
            echo "\033[0;34mNo update needed. You already have the latest stable release version $currentTag\033[0m"
            exit
        fi

        # Inform the user about the new tag.
        echo "\033[0;34mUpdating para from version $currentTag to the latest version $latestTag ...\033[0m"

        # Checkout the latest tag
        git checkout $latestTag --quiet
    else
        git fetch origin --quiet

        # Get the current commit.
        currentCommit=$(git rev-parse --short HEAD)

        # Get the latest commit.
        latestCommit=$(git rev-parse --short origin/master)

        if [ $currentCommit == $latestCommit ]; then
            echo "\033[0;34mThere are currently no updates available.\033[0m"
            exit
        else
            echo "\033[0;34mUpdating para from version $currentTag to the latest commit $latestCommit ...\033[0m"
            git checkout origin/master --quiet
        fi
    fi
fi

# Install dependencies.
echo "\033[0;34mUpdating dependencies...\033[0m"
composer install --no-dev --optimize-autoloader --quiet
cd $CURRENT_DIR 2>&1 > /dev/null

VERSION=$(para --version)
PARA_UPDATED=$?

if [ $PARA_UPDATED -eq 0 ]; then
    echo "\033[0;34mSuccessfully updated para to version ${VERSION} \033[0m"
    exit
fi
echo "\033[0;34mFailed to update para\033[0m"
