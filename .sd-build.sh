#!/bin/bash

set -e

# CUSTOMIZING
#
# This script by default builds the gulp JS and CSS files.
#
# 1. Leave SD_THEME blank if using yarn workspaces.  Otherwise, e.g. "Vendor/default".
#    (note: wildcards supported.)
# 2. Add any additional commands at the end.

SD_THEME="Travers/default"
SD_YARN="1.3.2"

unset NPM_CONFIG_PREFIX
curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.8/install.sh | dash
export NO_UPDATE_NOTIFIER=1
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
nvm install 8.15.1
nvm use 8.15.1

reportStats() {
    local error_code="$?"
    echo -e "\n\n.sd-build.sh exited: $error_code"

    if [ "$error_code" != "0" ]; then
        echo -e "\nIf the non-loop partition ran out of space or inodes, could be an issue:"
        echo "df -H:" && df -H
        echo "df -i:" && df -i
        echo "free -m:" && free -m

        echo -e "\n\n\nREMEMBER: Search for 'error' or 'fail' etc., earlier errors can cause later errors if they don't immediately fail."
    fi
}

# Report the stats above on error/exit.
trap reportStats 0

# Prevent vim from griping on every exit.
echo 'set viminfo=' >> ~/.vimrc

# Yarn refuses to install unless a profile exists.
touch ~/.profile
curl -o- -L https://yarnpkg.com/install.sh | bash -s -- --version "$SD_YARN"
export PATH="$HOME/.yarn/bin:$PATH"
yarn config set prefix "$HOME/.yarn"

# Add magento to path.
echo 'export PATH="$HOME/bin:$PATH"' >> .profile

echo -e "\nInstalling yarn dependencies..."
yarn
pushd vendor/snowdog/frontools; yarn; popd
if [ "$SD_THEME" != "" ]; then
    pushd vendor/somethingdigital/magento2-theme-bryantpark; yarn; popd
    # Allow wildcards by using a for.
    for d in app/design/frontend/$SD_THEME; do
        pushd $d; yarn; popd
    done
fi

echo -e "\nInstalling gem dependencies..."
bundle install

# ECE build moves frontend to init/pub/static, we need it in pub/static for the build.
# We detect the best strategy because ECE keeps changing things here.
REMOVE_PUB_STATIC="no"
HAS_PUB_FILES="`ls pub/static/frontend/*/*/*/requirejs-config{.min,}.js 2>/dev/null | wc -l`"
HAS_INIT_FILES="`ls init/pub/static/frontend/*/*/*/requirejs-config{.min,}.js 2>/dev/null | wc -l`"
if [ "$HAS_PUB_FILES" == "0" -a "$HAS_INIT_FILES" != "0" ]; then
    if [ ! -e pub/static/frontend ]; then
        mv init/pub/static/frontend pub/static/frontend
        REMOVE_PUB_STATIC="mv"
    else
        rsync -a init/pub/static/frontend/ pub/static/frontend/
        REMOVE_PUB_STATIC="rsync"
    fi
fi

echo -e "\nBuilding..."
yarn build:production

for f in `find pub/static/frontend/Travers/default/en_US/ReactPLP -name '*.js'`; do
    cp -f $f ${f/.js/.min.js};
done

for f in `find pub/static/frontend/Travers/default/en_US/ReactPLP -name '*.css'`; do
    cp -f $f ${f/.css/.min.css};
done

# Now, unfortunately, we need to take those built files into a separate directory.
# Cloud doesn't copy pub/ across.
if [ "$REMOVE_PUB_STATIC" == "no" ]; then
    rsync -a pub/static/ init/pub/static/
elif [ "$REMOVE_PUB_STATIC" == "rsync" ]; then
    rsync -a pub/static/frontend/ init/pub/static/frontend/
elif [ "$REMOVE_PUB_STATIC" == "mv" ]; then
    mv pub/static/frontend init/pub/static/frontend
else
    echo "ERROR: Unexpected REMOVE_PUB_STATIC."
    exit 1
fi

# Also setup the styleguide.
rsync -a pub/styleguide/ init/pub/styleguide/

ln -s ./media/robots.txt $MAGENTO_CLOUD_APP_DIR/pub/robots.txt
ln -s ./media/sitemap.xml $MAGENTO_CLOUD_APP_DIR/pub/sitemap.xml
