#!/usr/bin/env bash

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

if [ "$deps" == "cs-phpunit" ]; then
    vendor/bin/phpcs -p -n --colors --extensions=php,phtml --standard=vendor/magento-ecg/coding-standard/EcgM2 --ignore=./vendor,/Test $TRAVIS_BUILD_DIR
    vendor/bin/phpunit  --testdox --colors=always --debug
fi