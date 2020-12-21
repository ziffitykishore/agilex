# Generates files-remaining.txt, a list of all overridden files inside of this theme

find . -type f -name '*.phtml' | \
sed -E 's/^\.\///' | \
awk -F/ '{ temp = $1; sub("Magento_", "", temp); print "app/code/Magento/" temp "/view/frontend" substr($0, index($0, "/")); }' > files-remaining.txt
