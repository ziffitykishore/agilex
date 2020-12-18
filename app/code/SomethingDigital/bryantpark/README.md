# Something Digital Magento 2 "Bryant Park" Blank Theme [![Build Status](https://scrutinizer-ci.com/g/sdinteractive/SomethingDigital-Magento2-Theme-BryantPark/badges/build.png?b=develop&s=a7619a74a29a13c9e89502ff6cbbdf292c2b004e)](https://scrutinizer-ci.com/g/sdinteractive/SomethingDigital-Magento2-Theme-BryantPark/build-status/develop)

This should be used with the [Magento 2 Commerce Accelerator](https://github.com/sdinteractive/SomethingDigital-Magento2-Package-Accelerator).

## Creating a Child Theme

The Bryant Park theme should be used as the [parent of new themes](https://devdocs.magento.com/guides/v2.2/frontend-dev-guide/themes/theme-inherit.html). We've created a script that makes creating those "child" themes a little easier. 

```bash
$ cd vendor/SomethingDigital/SomethingDigital-Magento2-Theme-BryantPark/bin
$ ./createTheme.sh VendorName ThemeName
```
For example, `./createTheme.sh SomethingDigital DiscoveryPark` would create a theme called **SomethingDigital/DiscoveryPark**.

`ThemeName` is optional. `./createTheme.sh SomethingDigital` will create **SomethingDigital/default**.

> **TIP**: Make sure to run this within your Vagrant box ([see here](https://github.com/sdinteractive/SomethingDigital-Guides/blob/0676e800d32e7f3cbc4321e4526b1444011629f0/Workflows/Magento2/Installation.md)) - running the script directly on your machine won't work.

----

## Require.js integration

In the JS files in the theme, you can access require.js modules using their normal names.  Example:

```js
import customerData from 'Magento_Customer/js/customer-data';
console.log('customer info:', customerData.get('customer')());
```

Note that this will make that entire bundle (i.e. if this is in js/pdp/, all of the pdp js) wait for customer-data to load before running.  If you want to avoid this, use `window.require` which will happen at runtime.

## Bluefoot Integration

This theme comes with Bluefoot optimized styling. It should be used in conjuntion with the [SomethingDigital_Bluefoot](https://github.com/sdinteractive/Magento2_SomethingDigital_Bluefoot) module which adds [Catland](https://github.com/sdinteractive/SomethingDigital-FrontEndComponents/tree/master/src/catland) styling.

### Helper Classes
Class        | Description
:--------------- |:-----------
| `bluefoot--vertical-center` | Vertically align the row
| `bluefoot-row--reverse` | Reverse columns on mobile (for 2 column rows)

### Placeholder Images

This theme comes with placeholder Bluefoot content and the images for that content is within `./assets/pub-media-gene-cms/*.zip` such as `./assets/pub-media-gene-cms/b.zip`. You will want to unzip into `pub/media/gene-cms/`.


## Magento 2 GTM Integration

This theme includes GTM event integration through onclick dataLayer.push events. The table below defines the pages and actions included thus far.

Page/Section        | Action   | event | Element ID | eventCategory
:--------------- |:----------- | :----------------- | ---- | ----
✅ PDP   | Add to Cart | `pdp_addToCart` | `product-addtocart-button` | `addToCart`
✅ PDP   | Add to Wishlist | `pdp_addToWishlist` | `#product-addtowishlist-button` | `addToWishlist`
✅ PDP   | View Review | `pdp_viewReview` | `#product-view-reviews` | `viewReviews`
| | |
✅ Catalog    | Add to Wishlist | `catalog_addToWishlist` | `#catalog-to-wishlist` | `addToWishlist`
✅ Catalog    | View Reviews | `product_reviewsShort` | `#product-view-reviews`
| | |
✅ Minicart    | Show Minicart | `minicart_show` | `#minicart-show`
✅ Minicart    | Go to Checkout | `minicart_goToCheckout` | `#minicart-to-checkout` | `viewCeckout`
✅ Minicart    | View Cart | `minicart_viewCart` | n/a | `viewCart`
| | |
✅ Cart    | Go to Checkout | `cart_goToCheckout` | `#cart-checkout-button` | `viewCheckout`
✅ Cart    | Continue Shopping | `cart_continueShopping` | `#cart-continue-shopping-button`
