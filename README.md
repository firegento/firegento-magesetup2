FireGento_MageSetup
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-8-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->
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

## Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="https://github.com/kkrieger85"><img src="https://avatars2.githubusercontent.com/u/4435523?v=4" width="100px;" alt=""/><br /><sub><b>Kevin Krieger</b></sub></a><br /><a href="https://github.com/firegento/firegento-magesetup2/commits?author=kkrieger85" title="Documentation">📖</a></td>
    <td align="center"><a href="https://frostblog.net/"><img src="https://avatars3.githubusercontent.com/u/19548641?v=4" width="100px;" alt=""/><br /><sub><b>Jens Richter</b></sub></a><br /><a href="https://github.com/firegento/firegento-magesetup2/commits?author=frostblogNet" title="Code">💻</a></td>
    <td align="center"><a href="https://rouven.io/"><img src="https://avatars3.githubusercontent.com/u/393419?v=4" width="100px;" alt=""/><br /><sub><b>Rouven Alexander Rieker</b></sub></a><br /><a href="https://github.com/firegento/firegento-magesetup2/commits?author=therouv" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/demtaric"><img src="https://avatars3.githubusercontent.com/u/5658592?v=4" width="100px;" alt=""/><br /><sub><b>demtaric</b></sub></a><br /><a href="https://github.com/firegento/firegento-magesetup2/commits?author=demtaric" title="Code">💻</a></td>
    <td align="center"><a href="https://www.simonsprankel.com/"><img src="https://avatars1.githubusercontent.com/u/930199?v=4" width="100px;" alt=""/><br /><sub><b>Simon Sprankel</b></sub></a><br /><a href="https://github.com/firegento/firegento-magesetup2/commits?author=sprankhub" title="Code">💻</a></td>
    <td align="center"><a href="https://copex.io/"><img src="https://avatars1.githubusercontent.com/u/584168?v=4" width="100px;" alt=""/><br /><sub><b>Roman Hutterer</b></sub></a><br /><a href="https://github.com/firegento/firegento-magesetup2/commits?author=roman204" title="Code">💻</a></td>
    <td align="center"><a href="http://www.integer-net.de/agentur/andreas-von-studnitz/"><img src="https://avatars1.githubusercontent.com/u/662059?v=4" width="100px;" alt=""/><br /><sub><b>Andreas von Studnitz</b></sub></a><br /><a href="https://github.com/firegento/firegento-magesetup2/commits?author=avstudnitz" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/dh1984"><img src="https://avatars1.githubusercontent.com/u/6348686?v=4" width="100px;" alt=""/><br /><sub><b>Daniel</b></sub></a><br /><a href="https://github.com/firegento/firegento-magesetup2/commits?author=dh1984" title="Code">💻</a></td>
  </tr>
</table>

<!-- markdownlint-enable -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!