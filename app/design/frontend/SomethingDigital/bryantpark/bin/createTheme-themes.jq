. + {
  ($name): {
    "src": ("app/design/frontend/" + $package + "/" + $name),
    "dest": ("pub/static/frontend/" + $package + "/" + $name),
    "parent": "sd",
    "locale": ["en_US"],
    "lang": "scss",
    "postcss": ["plugins.autoprefixer()", "plugins.postcssInlineSvg()", "plugins.postcssObjectFitImages()"],
    "ignore": ["node_modules/**"]
  }
}
