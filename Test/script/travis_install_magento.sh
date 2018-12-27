#!/usr/bin/env bash
# Copyright © 2016 FireGento e.V.
# See LICENSE.md bundled with this module for license details.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

## setup magento installation

echo -e "\e[32m##############################"
echo -e "Install magento version $1"
echo -e "\e[32m##############################"

composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition:$1 $MAGENTO_ROOT
cd $MAGENTO_ROOT

echo -e "\e[32m############"
echo -e "Setup magento"
echo -e "\e[32m############"

php bin/magento setup:install --base-url="http://dummy.local/" --db-host="localhost" --db-name="magento" --db-user="root" --admin-firstname="admin"  --admin-lastname="admin" --admin-email="user@example.com" --admin-user="admin" --admin-password="admin123" --language="en_US" --backend-frontname="admin"


echo -e "\e[32m###########################"
echo -e "Install magesetup2 extension"
echo -e "\e[32m###########################"

composer config repositories.local path $TRAVIS_BUILD_DIR
composer require "firegento/magesetup2":"@dev"
php bin/magento module:enable FireGento_MageSetup
php bin/magento setup:upgrade


echo -e "\e[32m###########################"
echo -e "Run magesetup2 de"
echo -e "\e[32m###########################"
php bin/magento magesetup:setup:run de agreements
php bin/magento magesetup:setup:run de cms
php bin/magento magesetup:setup:run de email
php bin/magento magesetup:setup:run de systemConfig
php bin/magento magesetup:setup:run de tax

