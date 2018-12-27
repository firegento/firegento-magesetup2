#!/usr/bin/env bash
# Copyright © 2016 FireGento e.V.
# See LICENSE.md bundled with this module for license details.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

## setup magento installation
export MAGENTO_ROOT=`mktemp -d /tmp/mageteststand.XXXXXXXX`
echo "Install magento version $1 into $MAGENTO_ROOT"
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition:$1 $MAGENTO_ROOT
cd $MAGENTO_ROOT

php bin/magento setup:install --base-url="http://dummy.local/" --db-host="localhost" --db-name="magento" --db-user="root" --admin-firstname="admin"  --admin-lastname="admin" --admin-email="user@example.com" --admin-user="admin" --admin-password="admin123" --language="en_US" --backend-frontname="admin"

composer config repositories.local path $TRAVIS_BUILD_DIR

composer require "firegento/magesetup2":"@dev"

php bin/magento module:enable FireGento_MageSetup

php bin/magento setup:upgrade