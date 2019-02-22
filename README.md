# Something Digital Magento 2 Ecommerce Accelerator Package

Something Digital's standard Magento 2 setup. Makes use of Gulp, [Hologram](https://trulia.github.io/hologram/), SCSS and [Webpack](https://webpack.github.io/).

## Setup Instructions

### New Build

See [CHECKLIST.md](docs/CHECKLIST.md) for the steps to take when creating a new build based on the Accelerator Package.

Track your static content additions/updates in [static-content-log.md](docs/static-content-log.md). 

### How to Update to Latest Accelerator

If you need to update the Magento 2 build to latest version of accelerator, run the following commands:

```bash
git remote add accelerator git@github.com:sdinteractive/SomethingDigital-Magento2-Package-Accelerator.git
git fetch accelerator
git merge {version tag} --no-ff
```

### Things to note:
  - When making major version updates (e.g. v2.1.x to v2.2.x) patches should be removed from `composer.json` and the `composer-patches` directory.
  - Merge a tagged release instead of the latest from a branch
  - The generated commit message will be something like "Merge tag 'v1.2.1' into develop". Append "from accelerator" to it so that's it's clear.
  - If you run into a 500 Internal Server Error, please run `sudo a2enmod version` in your VM. Then restart apache. For existing boxes, this is caused by new versions of Magento using IfVersion in .htaccess. 

### Vagrant

Instructions on getting the Something Digital Magento 2 Vagrant box up and running can be found in the [Operations-Development](https://github.com/sdinteractive/Operations-Development/tree/master/boxes/Magento-BaseBuild2) repo.

Vagrant is Something Digital's preferred environment in which to develop Magento 2. If an alternative is needed, one can use the official [Magento 2 Docker DevBox](http://devdocs.magento.com/guides/v2.1/install-gde/docker/docker-over.html).

## Development

Magento 2 OOB themes are built with LESS. Due to various reasons we have decided to go with a Sass port of the Magento blank theme called [Magento2-Theme-BryantPark](https://github.com/sdinteractive/SomethingDigital-Magento2-Theme-BryantPark). It can be found in `vendor/somethingdigital/magento2-theme-bryantpark` and is a fork of the [Snowdog theme blank sass](https://github.com/SnowdogApps/magento2-theme-blank-sass).

### Gulp

Magento 2 OOB utilizes Grunt to pre-process the Blank & Luma LESS [themes and handle cache clearing](http://devdocs.magento.com/guides/v2.0/frontend-dev-guide/css-topics/css_debug.html#grunt_commands). For the Magento 2 base build, we will be utilizing the Snowdog [Frontools](https://github.com/SnowdogApps/magento2-frontools) package which utilizes Gulp.

In order to have a certain level of customization, the *SomethingDigital/bryantpark* theme inherits the Frontools Gulp file and adds its own set of tasks as listed in the table below.

##### Gulp Tasks

Run `gulp --tasks` to see full list of gulp tasks.

| Gulp Task Name        | Result            |
| -------------------   | ----------------- |
| `gulp svg`            | Combine theme SVGs into a **symbols.svg** file and place in `pub/static`.
| `gulp scripts`        | Run Webpack against scripts in `/js`, and place result in `pub/static`.
| `gulp styles`         | Process all `.scss` files.
| `gulp`                | Run all the above tasks.
| `gulp styleguide`     | Build the theme's Hologram style guide.
| `gulp watch`          | Start Browsersync and watch for style and JS changes.

> **TIP:** Want `gulp watch` to also compile the styleguide on each SCSS change? Run `gulp watch --styleguide`

> **TIP:** The theme's gulpfile can be run from the Magento root BUT it's located @ `vendor/somethingdigital/magento2-theme-bryantpark/gulpfile.js`


### JS & Webpack

The blank theme makes use of [Webpack](https://webpack.github.io/) to process and bundle its JS files. `gulp scripts` will start up Webpack using the `webpack.config.js` configuration file. If you need to create new bundled Webpack files, specify them in the configuration file under `entry`.

Note that in order to run Webpack you'll need to have the `pub/static` RequireJS config generated. This will automatically be created if you load the site in a browser, or it can be created from the CLI with an HTTPS-configured static content deploy: `export HTTPS=on; magento setup:static-content:deploy` / `magento sd:dev:static`.

### Enabling CSS / JS Minification

1. Make sure app/etc/config.local.php has been generated, i.e. via `magento app:config:dump`.
2. Update `minify_files` in app/etc/config.local.php to 1.
3. Remove `disableSuffix` from dev/tools/frontools/config/themes.json.

### Style Guide

Like in Magento 1, we utilize Hologram to build an HTML style guide for both QA & reference on what components are available in the theme. Check the styleguide before writing new Sass to ensure you are not able to extend existing styling.

##### How to access style guide?

Navigate to [https://magento2.dev/styleguide](https://magento2.dev/styleguide) and you can then open the left hamburger menu to jump between sections.

### Patch Application
Magento hotfix patches should be placed in `composer-patches` and added to `composer.json` with the following syntax:
```json
"extra": {
    ...
    "patches": {
        "magento-hotfix-patches": {
            "{{unique-patch-name}}": {
                "title": "{{patch-title}}",
                "url": "composer-patches/{{patch-filename}}",
                "type": "patch"
            },
        }
    }
    ...
}
```

## Coding Standards

For CSS, HTML, & JS SD best practices and coding styles, view the [SD Code Style document](https://github.com/sdinteractive/SomethingDigital-Guides/blob/master/Standards/CodeStyle.md).

**We use CSS & JS linting** and so code committed is expected to *PASS* the linters or a PR will likely have requested changes.

### Accessibility

Accessibility is becoming more important. When possible, utilize WCAG 2.0 AA standards.

- Aria tags, correct form markup for buttons, visuallyhidden tags for icon buttons, img alt tags, etc.

> **TIP:** For more information, checkout the SD accessibility [checklist](https://github.com/sdinteractive/SomethingDigital-Guides/tree/master/Standards/Accessibility).

## Help

It is HIGHLY recommended that you first read over the official documentation about Magento 2 development. [Frontend Developer Guide](http://devdocs.magento.com/guides/v2.1/frontend-dev-guide/bk-frontend-dev-guide.html) and/or [Backend (PHP) Developer Guide](http://devdocs.magento.com/guides/v2.1/extension-dev-guide/bk-extension-dev-guide.html).

We've also got a growing list of [Magento 2 guides](https://github.com/sdinteractive/SomethingDigital-Guides/tree/master/Workflows/Magento2) at the SD Guides repo on Github.
