# For each file in a list of remaining files that need to be upgraded,
# give a version diff in a Magento clone

# Versions to compare
VERSIONS_TO_COMPARE="2.2.4...2.2.7"

# Location of where your magento clone repository is
# If you're missing a magento clone on your machine, git clone https://github.com/magento/magento2.git
# Don't include quotes
MAGE_CLONE_LOCATION=~/mageDiff

for f in `cat files-remaining.txt`; do
   pushd $MAGE_CLONE_LOCATION > /dev/null # Or where ever the M2 clone is
   DIFF="`git diff -U0 $VERSIONS_TO_COMPARE -- $f | grep -vEe '^diff ' -e '^index ' -e '^--- a/' -e '^\+\+\+ b/' -e '^@@ ' -e '^- \* Copyright ' -e '^\+ \* Copyright '`"
   if [ "$DIFF" != "" ]; then
       git diff $VERSIONS_TO_COMPARE -- $f
   fi
   popd > /dev/null
done
