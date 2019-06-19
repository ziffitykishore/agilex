#!/bin/bash

set -e

# CUSTOMIZING
#
# This script by default builds the gulp JS and CSS files.
#
# Add any additional commands at the end.

SD_YARN="1.15.2"
SD_NODE="8.15.1"

reportStats() {
    local error_code="$?"
    echo -e "\n\n.sd-build.sh exited: $error_code"

    if [ "$error_code" != "0" ]; then
        echo -e "\nIf the non-loop partition ran out of space or inodes, could be an issue:"
        echo "df:" && df --output -h
        echo "free -m:" && free -m

        echo -e "\n\n\nREMEMBER: Search for 'error' or 'fail' etc., earlier errors can cause later errors if they don't immediately fail."
    fi
}

# Report the stats above on error/exit.
trap reportStats 0

# Prevent vim from griping on every exit, set some other settings.
echo 'set viminfo=' >> ~/.vimrc
echo 'syntax on' >> ~/.vimrc
echo 'set smartindent' >> ~/.vimrc
echo 'set expandtab' >> ~/.vimrc
echo 'set shiftwidth=4' >> ~/.vimrc
echo 'set tabstop=4' >> ~/.vimrc
echo 'set hlsearch' >> ~/.vimrc

# Yarn refuses to install unless a profile exists.
touch ~/.profile

# Start by installing node.
unset NPM_CONFIG_PREFIX
curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.8/install.sh | bash
export NO_UPDATE_NOTIFIER=1
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
nvm install "$SD_NODE"
nvm use "$SD_NODE"

curl -o- -L https://yarnpkg.com/install.sh | bash -s -- --version "$SD_YARN"
export PATH="$HOME/.yarn/bin:$PATH"
yarn config set prefix "$HOME/.yarn"

echo -e "\nInstalling yarn dependencies..."
yarn
pushd vendor/snowdog/frontools; yarn; popd

echo -e "\nInstalling gem dependencies..."
bundle install

# Assuming this is running before transfer and after generate, we can just work with pub/ in place.
echo -e "\nBuilding..."
yarn build:production

# Note: it's intentional that we use a relative path for the source.
# The build server uses a different absolute path than the actual webserver.
# Also: that we create pub/robots.txt means Magento's OOB generated robots.txt doesn't work anymore.  We may want to remove this now.
ln -s ./media/robots.txt $MAGENTO_CLOUD_APP_DIR/pub/robots.txt
ln -s ./media/sitemap.xml $MAGENTO_CLOUD_APP_DIR/pub/sitemap.xml
