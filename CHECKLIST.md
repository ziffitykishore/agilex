# New Project Checklist

## Repo

1. Create an empty new GitHub repo (follow naming convention.)
2. Locally, clone the SD Accelerator Package.
3. `git remote rm origin && git remote add origin <NEWREPOURL>`
4. Make sure to push only master and develop to the new repo.

Now you have a new repo.

## Files to Change

- Update `./README.md` with site name.
- Update `./.github/pull_request_template.md` with a ticket #.

## createTheme.sh
- CD to BryantPark bin directory: `cd ./vendor/somethingdigital/magento2-theme-bryantpark/bin`.
- Run the **createTheme.sh** shell file to create a new theme based off BryantPark/default: `./createTheme <VendorName>` replacing `<VendorName>` with something such as `./createTheme.sh Amazon` which will create `Amazon/default`.  This must be done **inside** the VM.
- After running **createTheme.sh**, make sure to update the files it asks.

## Install Sample Data

 - Check the spec.  If it doesn't say anything specific about sample data, add and commit:
 
       composer require magento/sample-data-media magento/module-catalog-sample-data magento/module-configurable-sample-data \
         magento/module-bundle magento/module-grouped-product-sample-data magento/module-gift-card-sample-data \
         magento/module-product-links-sample-data magento/module-review-sample-data magento/module-swatches-sample-data

 - If the project doesn't use bundles, gift cards, etc. remove them from the above command.
 - This will only create products/relationships, not other more dangerous sample data like discount rules.
 - Run setup:upgrade to actually create the data (this will also happen upon deploy.)
