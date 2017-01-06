# Something Digital Magento 2 Base Build

Welcome to the Something Digital Magento 2 Base Build. Yup, you read that right, Magento **2**! Lots has changed and this README will attempt to familiarize you, no matter your previous Magento experience.

### Preface

It is HIGHLY recommended that you first read over the official documentation about Magento 2 development. [Frontend Developer Guide](http://devdocs.magento.com/guides/v2.1/frontend-dev-guide/bk-frontend-dev-guide.html) and/or [Backend (PHP) Developer Guide](http://devdocs.magento.com/guides/v2.1/extension-dev-guide/bk-extension-dev-guide.html).

We've also got a growing list of [Magento 2 guides](https://github.com/sdinteractive/SomethingDigital-Guides/tree/master/Workflows/Magento2) at the SD Guides repo on Github.

## Vagrant Setup

Coming Soon-ish? For now, spin up a Vagrant box that at least meets the following recommendations.

- Apache2
- Mysql 5.6
- PHP7+
- Git
- Ruby (via RVM recommended)
- Node >5
- Yarn (replaces NPM)
- Composer

For Magento installation instructions, checkout the semi-detailed [SD guide](https://github.com/sdinteractive/SomethingDigital-Guides/tree/master/Workflows/Magento2/Installation.md).

## Initial Setup

Yarn, gem, composer...

## Development

### SASS VS LESS

Magento 2 OOB themes are built with LESS. Due to various reasons we have decided to go with the Snowdog open-source Sass port of the Magento blank theme. It can be found in `vendor/snowdog/theme-blank-sass`.

### Gulp

Magento 2 OOB utilizes Grunt to pre-process the Blank & Luma LESS [themes and handle cache clearing](http://devdocs.magento.com/guides/v2.0/frontend-dev-guide/css-topics/css_debug.html#grunt_commands). For the Magento 2 base build, we will be utilizing the Snowdog [Frontools](https://github.com/SnowdogApps/magento2-frontools) package which utilizes Gulp and the Snowdog [Sass blank theme](https://github.com/SnowdogApps/magento2-theme-blank-sass).

In order to have a certain level of customization, the *SomethingDigital/Blank* theme inherits the Frontools Gulp file and adds it's own set of tasks as listed in the table below.

##### Gulp Tasks

Run `gulp --tasks` to see full list of gulp tasks.

Task Name              | Unicode Value | What it do?!?
:---------------------- |:---: | :-----------------
`gulp sd:watch`        | 👀👀 | Hook into snowdog's watch task and add SVG and Styleguide rebuild.
`gulp sd:styleguide`   | 🎨 📓 | Build the theme's Hologram Style Guide
`gulp sd:svg`          | 🎏🆒 | Combine theme SVGs into a **symbols.svg** file and place in *pub/static*
`gulp sd:images`       | 📸 🔍 | Minify the theme */web/images* folder.

> **TIP:** The theme's gulpfile can be run from the Magento root BUT it's located @ `app/design/frontend/SomethingDigital/blank/gulpfile.js`


### JS & Webpack

🕳 n/a


### Style Guide

Like in Magento 1, we utilize Hologram to build an HTML Style Guide for both QA & reference on what components are available in the theme. Check the styleguide before writing new Sass to ensure you are not able to extend existing styling.

##### How to access style guide?

Navigate to [https://magento2.dev/styleguide](https://magento2.dev/styleguide) and you can then open the left hamburger menu to jump between sections.

## Coding Standards

For CSS, HTML, & JS SD best practices and coding styles, view the [SD Code Style document](https://github.com/sdinteractive/SomethingDigital-Guides/blob/master/Standards/CodeStyle.md).

**We use CSS & JS linting** and so code committed is expected to *PASS* the linters or a PR will likely have requested changes.

### Accessibility

Accessibility is becoming more important. When possible, utilize WCAG 2.0 AA standards.

- Aria tags, correct form markup for buttons, visuallyhidden tags for icon buttons, img alt tags, etc.

> **TIP:** For more information, checkout the SD accessibility [checklist](https://github.com/sdinteractive/SomethingDigital-Guides/tree/master/Standards/Accessibility).


## Deployments

🕳 n/a
