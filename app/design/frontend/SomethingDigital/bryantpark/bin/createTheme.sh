#!/bin/bash

# Fail on failures
set -e

files="../.editorconfig ../.babelrc ../.gitignore ../gulpfile.js ../package.json ../webpack.config.js ../yarn.lock ../js templates/*"
stylesFiles="../styles/styles.scss ../styles/styleguide*"
webContent="../web/svg"

# Allow this to be run from other paths.
BP_BIN="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
files="$BP_BIN/${files//[[:blank:]]/ $BP_BIN/}"
stylesFiles="$BP_BIN/${stylesFiles//[[:blank:]]/ $BP_BIN/}"
webContent="$BP_BIN/${webContent//[[:blank:]]/ $BP_BIN/}"

THEMES_JSON="$BP_BIN/../../../../dev/tools/frontools/config/themes.json"

function fn_help {
    echo "usage: createTheme package_name mytheme"
    echo "       will create package_name/mytheme"
}

function fn_copy {
    if [ ! -d $1 ]
    then
        echo "  — Creating theme package directory, ${theme_package}"
        mkdir -p $1
    fi

    echo "  — Copying files"
    cp -nr $files $1

    mkdir -p $theme_path/styles
    mkdir -p $theme_path/web
    cp -nr $stylesFiles $theme_path/styles
    cp -nr $webContent $theme_path/web
}

function fn_replace {
    echo "  — Setting name in theme.xml and registration.php"
    sed -i "s,PACKAGE,$package_name,g" $theme_path/theme.xml $theme_path/registration.php
    sed -i "s,THEMENAME,$theme_name,g" $theme_path/theme.xml $theme_path/registration.php
    echo "  — Setting name in gulpfile.js and webpack.config.js"
    sed -i "s:SomethingDigital/bryantpark:$theme_package:g" $theme_path/{gulpfile.js,webpack.config.js}
    magento_location="../../../../../"
    # TODO: change magentoLocation in gulpfile and webpack.config
    # sed -i "s:(^\s*magentoLocation: fs\.realpathSync\()(.+)(\),?$):\1$magento_location\2:" $theme_path/gulpfile.js
    echo "  — Setting name in package.json"
    sed -i "s/bryantpark/$lc_theme/" $theme_path/package.json
    echo "  — Setting path in root gulpfile.js"
    sed -i "s:/vendor/somethingdigital/magento2-theme-bryantpark:/app/design/frontend/$theme_package:" "$BP_BIN/../../../../gulpfile.js"
    echo "  — Updating hologram_config.yml"
    sed -i "s:SomethingDigital/bryantpark:$theme_package:g" "$BP_BIN/../../../../hologram/hologram_config.yml"
}

# Check for theme package name
if [ -z "$1" ]
then
  fn_help
  exit 1
else
  package_name=$1
fi

# Check for theme name
if [ -z "$2" ]
then
  fn_help
  exit 1
else
  theme_name=$2;
fi

theme_package="$package_name/$theme_name"
lc_theme=`echo $1|tr '[:upper:]' '[:lower:]'`
theme_path="$BP_BIN/../../../../app/design/frontend/$theme_package"

echo "Creating theme $theme_package:"
fn_copy $theme_path
fn_replace

echo -n "Updating themes.json: "
jq --indent 2 --arg package "$package_name"  --arg name "$theme_name" -f "$BP_BIN/createTheme-themes.jq" "$THEMES_JSON" > "$THEMES_JSON.temp"
if [ -e "$THEMES_JSON.temp" ]; then
  mv "$THEMES_JSON.temp" "$THEMES_JSON"
  echo "success."
else
  echo "failed."
fi

echo "Automated part is finished."
echo ""
echo "Now you have to do these things manually:"
echo "  — Update magentoLocation in gulpfile.js and webpack.config.js to ../../../../../"
echo "  — Update locales in dev/tools/frontools/config/themes.json"

