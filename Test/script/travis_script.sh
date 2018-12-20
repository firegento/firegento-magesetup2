#!/usr/bin/env bash

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

if [ "$TEST_SUITE" == "unit_cs" ]; then

    echo "Check EcgM2 standard"
    echo "####################"
    vendor/bin/phpcs -p -n --colors --extensions=php,phtml --standard=vendor/magento-ecg/coding-standard/EcgM2 --ignore=./vendor,/Test $TRAVIS_BUILD_DIR

    echo "Run unit tests"
    echo "###############"
    vendor/bin/phpunit
fi