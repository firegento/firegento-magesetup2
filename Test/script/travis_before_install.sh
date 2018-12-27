#!/usr/bin/env bash
# Copyright © 2016 FireGento e.V.
# See LICENSE.md bundled with this module for license details.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

## backup and disable xdebug
echo "backup and disable xdebug"
cp ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini ~/.phpenv/versions/$(phpenv version-name)/xdebug.ini.bak
echo > ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini
phpenv rehash

# create database and move db config into place
mysql -uroot -e '
  SET @@global.sql_mode = NO_ENGINE_SUBSTITUTION;
  CREATE DATABASE magento
'
