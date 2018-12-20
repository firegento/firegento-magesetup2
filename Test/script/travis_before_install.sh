#!/usr/bin/env bash

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

## backup and disable xdebug
cp ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini ~/.phpenv/versions/$(phpenv version-name)/xdebug.ini.bak
echo > ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini
phpenv rehash

# create directories for the tests
mkdir -p "$HOME/.php-cs-fixer"
mkdir -p build/logs

composer self-update