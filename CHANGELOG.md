# Changelog

All notable changes to this project will be documented in this file.

## [5.1.2] - 2021-10-12
[5.1.2]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/5.1.1...5.1.2

- Enabling/disabling the module will now properly update the profiler config in app/etc/env.php
- Fix fatal error on URL path page_cache/block/render

## [5.1.1] - 2021-08-05
[5.1.1]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/5.1.0...5.1.1

- Add Magento module dependencies in composer.json
- Code cleanup (CI pipelines integration)

## [5.1.0] - 2021-08-02
[5.1.0]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/5.0.2...5.1.0

- Add setting to toggle toolbar on admin area
- Add uninstallation procedure in the documentation

## [5.0.2] - 2021-01-04
[5.0.2]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/5.0.1...5.0.2

- Set minimum PHP version to 7.3
- Remove copyright/licence annotations from DocBlocks

## [5.0.1] - 2020-08-18
[5.0.1]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/5.0.0...5.0.1

- Fix event list not displayed in toolbar
- Remove type hinting from core methods and plugins

## [5.0.0] - 2020-08-17
[5.0.0]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/4.0.3...5.0.0

WARNING: Compatibility break with Magento 2.3

- Add type hinting and strict types
- Add escaping in templates
- Set minimum requirements to Magento 2.4

## [4.0.3] - 2019-11-26
[4.0.3]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/4.0.2...4.0.3

- Fix PHP notice triggered when an observer is disabled

## [4.0.2] - 2019-02-06
[4.0.2]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/4.0.1...4.0.2

- Fix composer dependencies

## [4.0.1] - 2019-01-16
[4.0.1]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/4.0.0...4.0.1

- Use Magento coding standard ruleset
- Update copyright

## [4.0.0] - 2018-11-28
[4.0.0]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/3.2.0...4.0.0

WARNING: Compatibility break with Magento 2.2

- The module is now compatible with Magento 2.3

## [3.2.0] - 2018-11-27
[3.2.0]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/3.1.1...3.2.0

- Code refactoring

## [3.1.1] - 2018-08-01
[3.1.1]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/3.1.0...3.1.1

- Fix issue #1 - remove highlighjs lib (for mysql queries) because of a requireJs incompatibility

## [3.1.0] - 2017-12-29
[3.1.0]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/3.0.1...3.1.0

- Implement new design
- Lots of minor fixes
- Better PHPDoc
- Update the copyright date
- Add doc

## [3.0.1] - 2017-12-12
[3.0.1]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/3.0.0...3.0.1

- Fix minor issue on preference table

## [3.0.0] - 2017-11-13
[3.0.0]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/1.2.2...3.0.0

WARNING: Compatibility break with Magento 2.1

- The module is now compatible with Magento 2.2
- Fix issue on message display conflict with the layout debug zone

## [1.2.2] - 2017-10-10
[1.2.2]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/1.2.1...1.2.2

- Fix issue on event name - must be in lower case

## [1.2.1] - 2017-10-09
[1.2.1]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/1.2.0...1.2.1

- Fix issue on layout zone - crash on the top menu

## [1.2.0] - 2017-09-29
[1.2.0]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/1.1.1...1.2.0

- Add the "observer" zone
- Add the "plugin" zone
- Add the "preference" zone
- Add the "layout" zone
  
## [1.1.1] - 2017-09-20
[1.1.1]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/1.1.0...1.1.1

- Fix bug on profiler class detection
- Fix bug on old toolbar cleaning
  
## [1.1.0] - 2017-09-20
[1.1.0]: https://github.com/Smile-SA/magento2-module-debug-toolbar/compare/1.0.0...1.1.0

- Add the "profiler" zone
- Refactoring the templates

## 1.0.0 - 2017-08-23

- First version of the module
- Add the "Generic" zone
- Add the "Request" zone
- Add the "Response" zone
- Add the "Mysql" zone
- Add the "Cache" zone
- Add the "Summary" zone
