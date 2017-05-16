FireGento_MageSetup
===================
MageSetup configures a shop for a national market. 

Currently supported countries are:
 
* Austria (at)
* Switzerland (ch)
* Germany (de)
* Spain (es)
* France (fr)
* United Kingdom (gb)
* Italy (it)
* Netherlands (nl)
* Poland (pl)
* Romania (ro)
* Russia (ru).

More countries to follow.

Missing your country? Please open a pull request with the necessary configuration for your country and help us support more countries.

Installation - Step by Step

- Edit the composer.json in MagentoRoot... (Copy and paste)
Should look like this:

"repositories": [
       {
           "type": "composer",
           "url": "https://repo.magento.com/"
       },
       {
           "type": "vcs",
           "url": "https://github.com/firegento/firegento-magesetup2"
       }
   ],

Open your Terminal/CLI and type in the following commands line by line without the leading "-"
- composer require firegento/magesetup2 @dev
- php bin/magento module:enable FireGento_MageSetup
- php bin/magento setup:upgrade
- php bin/magento magesetup:setup:run "language Code" -> without ""
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush

If you get the "Error 500" in Front/Backend you have to reset your file permission
- chown -R www-data:www-data /var/www/html/

Requirements
------------
- PHP >= 5.5.0

Installation
------------

### Via composer (recommended)

Please go to the Magento2 root directory and run the following commands in the shell:

```
composer config repositories.firegento_magesetup vcs git@github.com:firegento/firegento-magesetup2.git
composer require firegento/magesetup2:dev-develop
bin/magento module:enable FireGento_MageSetup
bin/magento setup:upgrade
bin/magento magesetup:setup:run <countrycode>
```

### Manually

Please create the directory *app/code/FireGento/MageSetup* and copy the files from this repository to the created directory. Then run the following commands in the shell:

```
bin/magento module:enable FireGento_MageSetup
bin/magento setup:upgrade
bin/magento magesetup:setup:run <countrycode>
```


Support
-------
If you encounter any problems or bugs, please create an issue on [GitHub](https://github.com/firegento/firegento-magesetup2/issues).

Contribution
------------
Any contribution to the development of MageSetup is highly welcome. The best possibility to provide any code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
FireGento Team
* Website: [http://firegento.com](http://firegento.com)
* Twitter: [@firegento](https://twitter.com/firegento)

Licence
-------
[GNU General Public License, version 3 (GPLv3)](http://opensource.org/licenses/gpl-3.0)

Copyright
---------
(c) 2015 FireGento Team
