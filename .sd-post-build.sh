#!/bin/bash

cp -R pub/static/frontend/Travers/default/en_US pub/static/frontend/Travers/default/en_US_Source

./.nvm/versions/node/v8.15.1/bin/r.js -o ./build.js baseUrl="pub/static/frontend/Travers/default/en_US_Source/" dir="pub/static/frontend/Travers/default/en_US/"

