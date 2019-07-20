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

Requirements
------------
- PHP     >= 7.1
- Magento >= 2.2.*

Installation
------------

### Via composer (recommended)

Please go to the Magento2 root directory and run the following commands in the shell:

```
composer require firegento/magesetup2
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

Maintainer
----------
* @sprankhub
* @frostblogNet
* @Schrank

Licence
-------
[GNU General Public License, version 3 (GPLv3)](http://opensource.org/licenses/gpl-3.0)

Copyright
---------
(c) 2015 FireGento Team
