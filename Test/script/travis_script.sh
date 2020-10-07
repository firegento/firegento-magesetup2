#!/usr/bin/env bash
# Copyright © FireGento e.V.
# See LICENSE.md bundled with this module for license details.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

cd $MAGENTO_ROOT

if [ "$CODE_QUALITY" == "true" ]; then

    echo -e "\e[32m##################"
    echo -e "Check code quality"
    echo -e "\e[32m##################"

    echo -e "\e[32m- checking php-cs"

    vendor/bin/php-cs-fixer fix --config=.php_cs.dist --dry-run --diff $TRAVIS_BUILD_DIR

    echo -e "\e[32m- checking magento cs"

    vendor/bin/phpcs -p --colors --extensions=php/php --standard=dev/tests/static/framework/Magento/ $TRAVIS_BUILD_DIR

fi

if [ "$UNIT_TEST" == "true" ]; then

    echo -e "\e[32m##############"
    echo -e "Run unit tests"
    echo -e "\e[32m##############"

    cp $TRAVIS_BUILD_DIR/phpunit.unittest.xml dev/tests/unit/phpunit.xml

    vendor/bin/phpunit -c dev/tests/unit/phpunit.xml --testsuite FireGento_MageSetup --debug --verbose

fi

if [ "$INTEGRATION_TEST" == "true" ]; then

    echo -e "\e[32m##############"
    echo -e "Run integration tests"
    echo -e "\e[32m##############"

    cp $TRAVIS_BUILD_DIR/install-config-mysql.php dev/tests/integration/etc/install-config-mysql.php
    cp $TRAVIS_BUILD_DIR/phpunit.integration.xml.dist dev/tests/integration/phpunit.xml

    vendor/bin/phpunit -c dev/tests/integration/phpunit.xml --testsuite FireGento_MageSetup --debug --verbose

fi
