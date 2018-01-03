# SomethingDigital_BryantPark

## Purpose

This is a module for BryantPark theme helpers. There are currently two:

- *Data.php* for getting a static path. Currently used with our SVG symbols map.
- *Social.php* for getting social network profiles of a particular store.

## Usage

### Setting Social Network Profiles

To set or change social network profiles, navigate to the admin then go to `Stores -> Configuration -> General -> Design` and under **Social Profiles URLs**, change or add the profiles as needed.

> Note: leave a social network blank in order for the icon to disappear on the frontend.

#### Networks Currently Supported
Facebook, Twitter, Instagram, Pinterest, Google Plus, Houzz, Snapchat, LinkedIn.

#### Corresponding Frontend Template

If you need to change the icons or order of social networks, the template can be found under `vendor/somethingdigital/magento2-theme-bryantpark/Magento_Theme/templates/html/social.phtml`.