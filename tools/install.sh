#!/usr/bin/env bash

para --version 2>&1 > /dev/null
if [ $? -eq 0 ]; then
    echo "\033[0;34mPara is already installed.\033[0m"
    exit
fi

CURRENT_DIR=$(pwd)

composer --version 2>&1 > /dev/null
COMPOSER_IS_AVAILABLE=$?

if [ ! $COMPOSER_IS_AVAILABLE -eq 0 ]; then
    echo "composer is not installed"
    exit
fi

git --version 2>&1 > /dev/null
GIT_IS_AVAILABLE=$?

if [ $GIT_IS_AVAILABLE -eq 0 ]; then
    echo "\033[0;34mCloning Para...\033[0m"
    hash git >/dev/null && /usr/bin/env git clone --depth=1 https://github.com/lrackwitz/para.git ~/.para || {
        echo "git is not installed"
        exit
    }
fi

echo "\033[0;34mInstalling dependencies...\033[0m"
cd ~/.para 2>&1 > /dev/null
composer install --no-dev --optimize-autoloader
cd $CURRENT_DIR 2>&1 > /dev/null

echo "\033[0;34mCreating symlink\033[0m"
ln -s ~/.para/bin/para /usr/local/bin/para
if [ $? -eq 0 ]; then
    echo "Symlink created at /usr/local/bin/para"
fi

para --version 2>&1 > /dev/null
PARA_INSTALLED=$?

if [ $PARA_INSTALLED -eq 0 ]; then
    echo "\033[0;34mPara installed successfully\033[0m"
    exit
fi
echo "\033[0;34mFailed to install para\033[0m"
