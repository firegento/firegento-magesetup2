#!/usr/bin/env bash
# Copyright © 2016 FireGento e.V.
# See LICENSE.md bundled with this module for license details.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

if [ "$CODE_QUALITY" == "true" ]; then

    echo -e "\e[32mCheck code quality"
    echo -e "\e[32m####################"




    echo -e "\e[32m- checking ecgM2"

    cd $MAGENTO_ROOT
    composer require "magento-ecg/coding-standard":"^3.0"
    vendor/bin/phpcs -p -n --colors --extensions=php,phtml --standard=vendor/magento-ecg/coding-standard/EcgM2 --ignore=./vendor,/Test $TRAVIS_BUILD_DIR

    echo -e "\e[32m- checking php-cs"

    cd $MAGENTO_ROOT
    composer require "friendsofphp/php-cs-fixer":"^2.2"
    vendor/bin/php-cs-fixer fix --config=.php_cs.dist --dry-run --diff $TRAVIS_BUILD_DIR

    echo -e "\e[32m- checking magento cs"

    cd $MAGENTO_ROOT
    ls -la .
    ls -la dev
    ls -la dev/tests
    #vendor/bin/phpcs -p --colors --extensions=php/php --standard=dev/tests/static/framework/Magento/ $TRAVIS_BUILD_DIR





fi

echo -e "\e[32mRun unit tests"
echo -e "\e[32m###############"
cp ${TRAVIS_BUILD_DIR}/phpunit.unittest.xml dev/tests/unit/phpunit.xml
php bin/magento dev:tests:run unit