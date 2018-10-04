# m2-weltpixel-layered-navigation

### Installation

Dependencies:
 - m2-weltpixel-backend

With composer:

```sh
$ composer config repositories.welpixel-m2-weltpixel-layered-navigation git git@github.com:rusdragos/m2-weltpixel-layered-navigation.git
$ composer require weltpixel/m2-weltpixel-layered-navigation:dev-master
```

Manually:

Copy the zip into app/code/WeltPixel/LayeredNavigation directory


#### After installation by either means, enable the extension by running following commands:

```sh
$ php bin/magento module:enable WeltPixel_LayeredNavigation --clear-static-content
$ php bin/magento setup:upgrade
```

