#!/usr/bin/env bash
# Copyright © 2016 FireGento e.V.
# See LICENSE.md bundled with this module for license details.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

if [ "$CODE_QUALITY" == "true" ]; then

    echo "Check code quality"
    echo "####################"

    composer require "magento-ecg/coding-standard": "^3.0"

    echo "- checking ecgM2"

    $MAGENTO_ROOT/vendor/bin/phpcs -p -n --colors --extensions=php,phtml --standard=vendor/magento-ecg/coding-standard/EcgM2 --ignore=./vendor,/Test $TRAVIS_BUILD_DIR

    echo "- checking php-cs"

    $MAGENTO_ROOT/vendor/bin/php-cs-fixer fix --config=.php_cs.dist --dry-run --diff $TRAVIS_BUILD_DIR

    echo "- checking magento cs"
    $MAGENTO_ROOT/vendor/bin/phpcs -p --colors --extensions=php/php --standard=dev/tests/static/framework/Magento/ $TRAVIS_BUILD_DIR

fi

echo "Run unit tests"
echo "###############"

php bin/magento dev:tests:run unit