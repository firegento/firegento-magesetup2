#!/usr/bin/env bash
# Copyright © 2016 FireGento e.V.
# See LICENSE.md bundled with this module for license details.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

if [ "$TEST_SUITE" == "unit_cs" ]; then

    echo "Check EcgM2 standard"
    echo "####################"
    $MAGENTO_ROOT/vendor/bin/phpcs -p -n --colors --extensions=php,phtml --standard=vendor/magento-ecg/coding-standard/EcgM2 --ignore=./vendor,/Test $TRAVIS_BUILD_DIR

    $MAGENTO_ROOT/vendor/bin/php-cs-fixer fix --config=.php_cs.dist --dry-run --diff $TRAVIS_BUILD_DIR

    ${MAGENTO_ROOT}vendor/bin/phpcs -p --colors --extensions=php/php --standard=dev/tests/static/framework/Magento/ $MAGENTO_ROOT/vendor/firegento/magesetup2/

    echo "Run unit tests"
    echo "###############"
    vendor/bin/phpunit
fi