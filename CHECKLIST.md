# New Project Checklist

## Repo

1. Create an empty new GitHub repo (follow naming convention.)
2. Locally, clone the SD Accelerator Package.
3. `git remote rm origin && git remote add origin <NEWREPOURL>`
4. Make sure to push only master and develop to the new repo.

Now you have a new repo.

## Files to Change

- Update `./README.md` with site name.
- Run `./vendor/somethingdigital/magento2-theme-bryantpark/bin/createTheme.sh` to create a new theme based off BryantPark.
- After running `createTheme.sh`, make sure to update the files it asks.

## TODO

 * Files that need to be changed.
 * Simple things that must be added.
 * CW # in .github/.
