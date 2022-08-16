# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)

## [Unreleased]

### Added

- ...

### Changed

- ...

### Removed

- ...

## [v1.1.1] - 2022-08-16

### Added

- dedicated usage section in README (thanks @therouv)

### Changed

- fix XSD reference in `etc/magesetup.xml` [#208](https://github.com/firegento/firegento-magesetup2/pull/208) (thanks @sprankhub)

## [v1.1.0] - 2022-03-31

### Added

- support for PHP 8.1 [#207](https://github.com/firegento/firegento-magesetup2/pull/207) (thanks @domeglic)

## [v1.0.0] - 2021-08-19

### Added

- support for Magento 2.4.3 [#200](https://github.com/firegento/firegento-magesetup2/pull/200) (thanks @sprankhub)

### Changed

- fatal error on all frontend pages [#199](https://github.com/firegento/firegento-magesetup2/issues/199) (thanks @sprankhub)

### Removed

- support for Magento < 2.4.3 [#200](https://github.com/firegento/firegento-magesetup2/pull/200) (thanks @sprankhub)

## [v0.4.1] - 2020-11-04

### Changed

- Registry key already exists error for HTTPS sites [#186](https://github.com/firegento/firegento-magesetup2/pull/186) (thanks @sprankhub)
- Wrong attribute group name in setup scripts [#187](https://github.com/firegento/firegento-magesetup2/pull/187) (thanks @norgeindian)

## [v0.4.0] - 2020-10-08

### Added

- Support for Magento 2.4 [#183](https://github.com/firegento/firegento-magesetup2/pull/183) (thanks @sprankhub)
- Support for PHP 7.4 [#183](https://github.com/firegento/firegento-magesetup2/pull/183) (thanks @sprankhub)

## [v0.3.0] - 2020-06-30

### Added

- Add all contributors and bot config (thanks @kkrieger85)
- Add integration test (thanks @sprankhub,@BorisovskiP)
- Add integration test to travis ci [#166](https://github.com/firegento/firegento-magesetup2/issues/166) (thanks @frostblogNet)
- Add unit test form blocks (thanks @frostblogNet)
- Add integration test for visible in checkout property (thanks @sprankhub)

### Changed

- Fix imprint blocks in mails [#149](https://github.com/firegento/firegento-magesetup2/issues/149) (thanks @sprankhub)
- Hide tax details for grouped product [#150](https://github.com/firegento/firegento-magesetup2/issues/150) (thanks @sprankhub)
- Fix setting of tax class ID on products (thanks @sprankhub)
- Configure kg as weight unit during setup [#31](https://github.com/firegento/firegento-magesetup2/issues/31) (thanks @sprankhub)
- Use delivery time label from attribute config (Note you have to configure the attribute lable in the admin area) [#124](https://github.com/firegento/firegento-magesetup2/issues/124) (thanks @sprankhub)
- Use newer coding magento standard (thanks @sprankhub)
- Update English translation (thanks @sprankhub)
- Update German translation (thanks @sprankhub)
- Replace around with after plugin (thanks @sprankhub)

### Removed

- Remove price details from tier prices [#87](https://github.com/firegento/firegento-magesetup2/issues/87) (thanks @sprankhub)
- Drop support for magento 2.2.x and php 7.1.x (thanks @sprankhub + @frostblogNet)
- Remove unused Dutch translation (thanks @sprankhub)
