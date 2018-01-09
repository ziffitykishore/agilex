# SomethingDigital_AssetOptimizer

## Purpose

This module allows for static content (KO HTML, JS, CSS, etc.) to be symlinked within the pub directory to the actual file. By symlinking we will no longer need to redeploy the static content on every file change and instead can simply refresh the page. This greatly speeds up KO & JS development.

## Usage

### Frontend

To deploy static content using the SD AssetOptimizer, run the following commands within the Vagrant box:

First delete the existing static content by running:
```bash
gulp clean
```

Next, deploy the static content (replace **Package/themeName** with *SomethingDigital/bryantpark* for example):
```bash
magento sd:dev:static Package/themeName
```

Finally, flush the Magento cache:
```bash
magento cache:flush
```

> Protip: Re-run the sd:dev:static command whenever you run a setup:upgrade as the pub static content will be cleared out.

### Adminhtml

Deploying the adminhtml static content will greatly improve local load times within the admin.

Simple run:
```bash
magento sd:dev:static --area=adminhtml Magento/backend
```