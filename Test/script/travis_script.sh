#!/usr/bin/env bash

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

if [ "$deps" == "codeSniffer" ]; then
    vendor/bin/phpcs -p -n --colors --extensions=php,phtml --standard=vendor/magento-ecg/coding-standard/EcgM2 $TRAVIS_BUILD_DIR
fi

if [ "$deps" == "high" ]; then
    echo "here";
fi