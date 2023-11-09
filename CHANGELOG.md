## v2.0.1 - 2023-11-09

### [2.0.1](https://github.com/openedx/openedx-wordpress-ecommerce/compare/v2.0.0...v2.0.1) (2023-11-09)

### Bug Fixes

- file and class names corrected based on wordpress requirements ([#53](https://github.com/openedx/openedx-wordpress-ecommerce/issues/53)) ([79bdd6a](https://github.com/openedx/openedx-wordpress-ecommerce/commit/79bdd6a0a6127cc1eaf36a90b0451b0ffb36613b))

## v2.0.0 - 2023-11-08

### [2.0.0](https://github.com/openedx/openedx-wordpress-ecommerce/compare/v1.15.3...v2.0.0) (2023-11-08)

#### ⚠ BREAKING CHANGES

- this change causes old enrollment request entries not to be displayed because they do not have status

#### Features

- show enrollment status in enrollments table ([#56](https://github.com/openedx/openedx-wordpress-ecommerce/issues/56)) ([b1348f3](https://github.com/openedx/openedx-wordpress-ecommerce/commit/b1348f3dfe51a4bc9dfa64dc0ea26cc5bea0663f))

## v1.15.3 - 2023-11-08

### [1.15.3](https://github.com/openedx/openedx-wordpress-ecommerce/compare/v1.15.2...v1.15.3) (2023-11-08)

### Bug Fixes

- undefined variable enrollment action in new enrollment request ([#60](https://github.com/openedx/openedx-wordpress-ecommerce/issues/60)) ([902882e](https://github.com/openedx/openedx-wordpress-ecommerce/commit/902882e00a0ecb9e9f7fdfb1da4ad2c5d5acfbe0))

## v1.15.2 - 2023-11-08

### [1.15.2](https://github.com/openedx/openedx-wordpress-ecommerce/compare/v1.15.1...v1.15.2) (2023-11-08)

### Bug Fixes

- avoid using the email enrollment endpoint to fix a bug in versions without email support ([#59](https://github.com/openedx/openedx-wordpress-ecommerce/issues/59)) ([563e295](https://github.com/openedx/openedx-wordpress-ecommerce/commit/563e2954b97dc674b4fb8d24d5862a4f5d703379))

### Documentation

- add the first ADRs ([#55](https://github.com/openedx/openedx-wordpress-ecommerce/issues/55)) ([f240dc0](https://github.com/openedx/openedx-wordpress-ecommerce/commit/f240dc09e4b14651bb1a78984b4102e408326d04))

## v1.15.1 - 2023-11-02

### [1.15.1](https://github.com/openedx/openedx-wordpress-ecommerce/compare/v1.15.0...v1.15.1) (2023-11-02)

### Bug Fixes

- plugin working on clean installation ([#58](https://github.com/openedx/openedx-wordpress-ecommerce/issues/58)) ([f530acd](https://github.com/openedx/openedx-wordpress-ecommerce/commit/f530acdf4df951da2c90c1ddbc6e6e1fb40c63f7))

## v1.15.0 - 2023-11-02

### [1.15.0](https://github.com/openedx/openedx-wordpress-ecommerce/compare/v1.14.3...v1.15.0) (2023-11-02)

#### Features

- created new workflow for release zip with current version ([#54](https://github.com/openedx/openedx-wordpress-ecommerce/issues/54)) ([2365439](https://github.com/openedx/openedx-wordpress-ecommerce/commit/23654391f078165f0f4b5c5e9c71bd5ae5971467))

#### Build Systems

- Creating workflow `add-depr-ticket-to-depr-board.yml`. ([8419386](https://github.com/openedx/openedx-wordpress-ecommerce/commit/84193865b21a7c9be30882229bd2136381cc8c17))
- Creating workflow `add-remove-label-on-comment.yml`. ([de0da1c](https://github.com/openedx/openedx-wordpress-ecommerce/commit/de0da1c7106f904d9604a510113246031c5c2844))
- Creating workflow `commitlint.yml`. ([ce85c30](https://github.com/openedx/openedx-wordpress-ecommerce/commit/ce85c3081b3d104e9926f977eec2f8b86ab2a814))
- Creating workflow `self-assign-issue.yml`. ([a413093](https://github.com/openedx/openedx-wordpress-ecommerce/commit/a4130939e63bfa2cee932e3c45893677ac431550))

## v1.14.3 - 2023-10-13

### [1.14.3](https://github.com/eduNEXT/openedx-wordpress-ecommerce/compare/v1.14.2...v1.14.3) (2023-10-13)

### Bug Fixes

- added missing parameter to process enrollment force ([#51](https://github.com/eduNEXT/openedx-wordpress-ecommerce/issues/51)) ([d92ee1c](https://github.com/eduNEXT/openedx-wordpress-ecommerce/commit/d92ee1c2f5eb47bb576641d35fc7432ac8716354))

## v1.14.2 - 2023-10-13

### [1.14.2](https://github.com/eduNEXT/openedx-wordpress-ecommerce/compare/v1.14.1...v1.14.2) (2023-10-13)

### Bug Fixes

- corrected main file name in ci github workflow ([#50](https://github.com/eduNEXT/openedx-wordpress-ecommerce/issues/50)) ([340a151](https://github.com/eduNEXT/openedx-wordpress-ecommerce/commit/340a151cdaf16138d0841d9a626715f92a0b78b6))
- updating readme and plugin definition to comply with Wordpress naming conventions ([#49](https://github.com/eduNEXT/openedx-wordpress-ecommerce/issues/49)) ([5716371](https://github.com/eduNEXT/openedx-wordpress-ecommerce/commit/571637196c4472899004c3f146a011932a6773ab))

## v1.14.1 - 2023-10-12

### [1.14.1](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.14.0...v1.14.1) (2023-10-12)

### Bug Fixes

- improve tooltip info, examples in the domain settings and doc about save before generate token ([#48](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/48)) ([2857e61](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/2857e61834f393a52855f2410e9ff25219de6174))

### Documentation

- update the readme.txt and the plugin.php to improve the launch in WordPress ([#47](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/47)) ([e32f419](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/e32f41974b88c3648cace935081748de7fd3e989))

## v1.14.0 - 2023-10-12

### [1.14.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.13.0...v1.14.0) (2023-10-12)

#### Features

- enroll and unenroll process with new and old endpoints ([#44](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/44)) ([85d69b0](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/85d69b02272b8c8a1abf0d3c9c89fb87aa764312))

## v1.13.0 - 2023-10-10

### [1.13.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.12.1...v1.13.0) (2023-10-10)

#### Features

- course id fields restriction & js uncaught element bug fixed ([#46](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/46)) ([9551c99](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/9551c991e00c7865a4abc836bcfadd97aef71cb4))

## v1.12.1 - 2023-10-10

### [1.12.1](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.12.0...v1.12.1) (2023-10-10)

### Bug Fixes

- access token doesn't generate errors when token expires ([#45](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/45)) ([594cd31](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/594cd312506c1291a370f7653fbb299a1415e0fa))

## v1.12.0 - 2023-10-06

### [1.12.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.11.1...v1.12.0) (2023-10-06)

#### Features

- checkbox to enable options if product is a course ([#42](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/42)) ([5fe899b](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/5fe899b2e979cd9d5a27907fc744b781ec477a56))

## v1.11.1 - 2023-10-05

### [1.11.1](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.11.0...v1.11.1) (2023-10-05)

### Bug Fixes

- bugs in product table fixed & form changes in settings and enrollment ([#43](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/43)) ([ddaaba7](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/ddaaba794653f3203ddab352325d657e287b2803))

## v1.11.0 - 2023-09-27

### [1.11.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.10.0...v1.11.0) (2023-09-27)

#### Features

- unenroll request when a refund is processed ([#41](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/41)) ([6021b6c](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/6021b6c80beb35a5bddaaa05103219915400760d))

## v1.10.0 - 2023-09-25

### [1.10.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.9.2...v1.10.0) (2023-09-25)

#### Features

- enrollment creation when an order is processed ([#40](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/40)) ([729ae47](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/729ae477c83ddd22f0dfa125d18819bd7004ce66))

#### Documentation

- add how-tos and the base of the quickstart ([#39](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/39)) ([871b871](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/871b87195a6686fe5792b3acbb8b0c1752b40d68))

## v1.9.2 - 2023-09-12

### [1.9.2](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.9.1...v1.9.2) (2023-09-12)

### Bug Fixes

- handler of fatal error in settings page corrected ([#38](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/38)) ([c979f0b](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/c979f0bbc7d62a84dcb10c1a974f38323e16cf53))

## v1.9.1 - 2023-09-08

### [1.9.1](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.9.0...v1.9.1) (2023-09-08)

### Bug Fixes

- course_id read-only, naming improvements and css styles ([#37](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/37)) ([db1a2b1](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/db1a2b12ca8ac70aefd32d3941d11411a96ad085))

### Code Refactoring

- enrollment request form changes ([#36](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/36)) ([66e2643](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/66e264307f9686e3e36da4956f2df8d0901467d7))

## v1.9.0 - 2023-09-01

### [1.9.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.8.0...v1.9.0) (2023-09-01)

#### Features

- enrollment with api requests ([#35](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/35)) ([e6ad1cd](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/e6ad1cdc03b41dcfe0e551baeba0f508579501f5))

#### Documentation

- fix warnings in the docs compilation ([#33](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/33)) ([22daabd](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/22daabd0269bc956f8713a6954f1f2c734176459))

## v1.8.0 - 2023-08-31

### [1.8.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.7.0...v1.8.0) (2023-08-31)

#### Features

- wpcs workflow and code quality improved ([#31](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/31)) ([50f9cb1](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/50f9cb14b6d64d048ec31a63e264ec8a58712e19))

#### Bug Fixes

- ci and wpcs workflow fixes ([#34](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/34)) ([847d180](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/847d180d647fb2c977dd07c85494c520f6aa3a25))

## v1.7.0 - 2023-08-30

### [1.7.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.6.0...v1.7.0) (2023-08-30)

#### Features

- release generates zip with dependencies ([#32](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/32)) ([76e228d](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/76e228d24392994184d521472d05f96c5d75e5f0))

## v1.6.0 - 2023-08-28

### [1.6.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.5.0...v1.6.0) (2023-08-28)

#### Features

- manual token generation ([#25](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/25)) ([66b2ea9](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/66b2ea9b126e746fec0f09cae7b3f6526623e574))

#### Documentation

- add readthedocs file ([#30](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/30)) ([373e545](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/373e545c3fa151981e503b897f5c61de360fb198))
- add the base for the documentation ([#24](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/24)) ([c762cb0](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/c762cb024005cc96cde9786e65dfe52f977381f9))

## v1.5.0 - 2023-08-23

### [1.5.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.4.0...v1.5.0) (2023-08-23)

#### Features

- settings section for openedx plugin configuration ([#23](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/23)) ([10a31c3](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/10a31c332caa03c564c03c41e3dc25de14de2a4c))

## v1.4.0 - 2023-08-15

### [1.4.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.3.0...v1.4.0) (2023-08-15)

#### Features

- enable redirection button to order or enrollment request ([#20](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/20)) ([fc3fa59](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/fc3fa59ebf1eb93e122b042e7359a8b778f126d9))

## v1.3.0 - 2023-08-04

### [1.3.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.2.0...v1.3.0) (2023-08-04)

#### Features

- user friendly way to add atributes to a product ([#18](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/18)) ([7103742](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/7103742e953575ea58e9127ef65c6c9521afecfd))

## v1.2.0 - 2023-08-03

### [1.2.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.1.1...v1.2.0) (2023-08-03)

#### Features

- implement logging system for Enrollment Requests ([#17](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/17)) ([9d05522](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/9d055228330920e84fef577c6aa7a5275e76ae75))

#### Documentation

- add READMEs ([#19](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/19)) ([93d1429](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/93d1429209a5dbcfc2a674d4cdfee8f7d302c27d))

#### Styles

- ergonomic design form ([#16](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/16)) ([e5215ab](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/e5215abef6d43e75a3750ffdfd9f23458204dfff))

## v1.1.1 - 2023-07-21

### [1.1.1](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.1.0...v1.1.1) (2023-07-21)

### Bug Fixes

- enrollment crud operations working correctly ([#15](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/15)) ([ab972ec](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/ab972ecbbe51d1a17e557eb0805bbd9b7f0f2db2))

## v1.1.0 - 2023-07-07

### [1.1.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.0.0...v1.1.0) (2023-07-07)

#### Features

- continuous integration workflow ([#13](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/13)) ([fbe5e10](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/fbe5e1080ebe2efb6901dbe10e287a91e4d2e7d8))

#### Bug Fixes

- code refactoring and namespace/use statements. ([#11](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/11)) ([7e280e4](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/7e280e4d5a30b9b4b29ec7f60cea7cb7b75aadc3))
- correct the ci workflows ([#14](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/14)) ([a8c2bb4](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/a8c2bb4b07b323777b3c5a53fc9ef9509db29df3))
- corrected file names according to the class they contain ([#10](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/10)) ([6bd562c](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/6bd562cbf7b828a32bae6c12bb44026be995d5a9))

#### Tests

- implement unit testing for post-type enrollment ([#5](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues/5)) ([5398e66](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/5398e668d1e7b8c440a85aec5dde1882e3f997cd))

## Release v1.0.0

## [1.0.0](https://github.com/eduNEXT/openedx-woocommerce-plugin/compare/v1.0.0...v1.0.0) (2023-07-05)

### Features

- initial commit ([28de3be](https://github.com/eduNEXT/openedx-woocommerce-plugin/commit/28de3be9ff181986c84f5fef9c9ee1e6fa3706dd))
