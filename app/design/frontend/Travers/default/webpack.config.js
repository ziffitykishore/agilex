/**
 * Our gulpfile.js changes the directory to
 * SnowDog's frontools, but that breaks
 * our Webpack script. Changing working
 * directory here back to SomethingDigital's
 * folder.
 */
process.chdir(__dirname);

const path = require('path');
const webpack = require('webpack');
const fs = require('fs');
const util = require('gulp-util');
const RequireJsLoaderPlugin = require('@sdinteractive/requirejs-loader').RequireJsLoaderPlugin;
const RequireJsResolverPlugin = require('@sdinteractive/requirejs-resolver');
const RequireJsExportPlugin = require('@sdinteractive/requirejs-export-plugin');

/*
    SETTINGS
*/
const settings = {
  themeName: 'Travers/default',
  themeLanguage: 'en_US',
  // path to the Magento root
  magentoLocation: '../../../../../',
};

/*
    PATHS
*/
const PUBLIC_STATIC_DIR = `pub/static/frontend/${settings.themeName}/${settings.themeLanguage}`;

// Magento 2.1.x and 2.2.x use different paths, and 2.1.9 changed it from both.
// We also have to deal with minified files.
const requirejs_dirs = [
  `pub/static/_requirejs/frontend/${settings.themeName}/${settings.themeLanguage}`,
  `pub/static/frontend/${settings.themeName}/${settings.themeLanguage}`
];
const requirejs_filenames = [
  'requirejs-config.min.js',
  'secure/requirejs-config.min.js',
  'requirejs-config.js',
  'secure/requirejs-config.js'
];
const REQUIREJS_CONFIG_DEFAULT = `pub/static/frontend/${settings.themeName}/${settings.themeLanguage}/requirejs-config.js`;
let REQUIREJS_CHOSEN_CONFIG = REQUIREJS_CONFIG_DEFAULT;
for (const dir of requirejs_dirs) {
  const full_dir = `${settings.magentoLocation}${dir}/`;
  const found = requirejs_filenames.map(path => fs.existsSync.call(fs, full_dir + path) ? `${dir}/${path}` : false).filter(Boolean);
  if (found.length) {
      REQUIREJS_CHOSEN_CONFIG = found[0];
      break;
  }
}
if (!fs.existsSync(settings.magentoLocation + REQUIREJS_CHOSEN_CONFIG)) {
  console.log('\x1b[31m', 'RequireJS config is missing, unable to run Webpack. To generate config file, please run: php bin/magento sd:dev:static ' + settings.themeName, '\x1b[0m');
    process.exit(1);
}

module.exports = {
  mode: util.env.prod ? 'production' : 'development',

  devtool: 'cheap-source-map',

  entry: {
    category: './js/category/index.js',
    checkout: './js/checkout/index.js',
    cms: './js/cms/index.js',
    home: './js/home/index.js',
    pdp: './js/pdp/index.js',
    search: './js/search/index.js',
    registration: './js/registration/index.js',

    // This is combined into common.js in gulp
    global: './js/global/index.js',
  },

  output: {
    filename: '[name].js',
    path: path.resolve(settings.magentoLocation, PUBLIC_STATIC_DIR, 'js/dist'),
    publicPath: PUBLIC_STATIC_DIR + '/js/dist',
  },

  module: {
    rules: [
      {
        test: [
          /svg4everybody/
        ],
        use: [
          { loader: 'imports-loader', options: 'this=>window' },
        ],
      },
      {
        test: /\.js$/,
        exclude: [
          function (path) {
            // Unfortunate that we have jquery installed via yarn, confuses things.
            if (/node_modules[\\\/](jquery|underscore)[\\\/]/.test(path)) {
              return false;
            }

            return /(node_modules|bower_components)/.test(path);
          },
          path.resolve(__dirname, 'js'),
        ],
        use: [
          { loader: '@sdinteractive/requirejs-loader' },
        ],
      },
      {
        test: /\.js$/,
        // Running Babel against all JS files can
        // cause some issues
        include: path.resolve(__dirname, 'js'),
        use: [
          { loader: 'babel-loader' },
        ],
      },
    ],
  },

  optimization: {
    splitChunks: {
      chunks: 'all',
      minChunks: Infinity,
      minSize: 1024,
      name: (module, chunks, key) => 'common-partial',
    },
    namedChunks: true,
  },

  plugins: [
    new RequireJsExportPlugin(),
    new RequireJsLoaderPlugin(),

    // Progress bar. Although Webpack has a --progress flag,
    // it's not usable with the webpack-stream Gulp package.
    new webpack.ProgressPlugin(function (percentage, message) {
      if (process.stderr.isTTY) {
        const percent = Math.round(percentage * 100);
        process.stderr.clearLine();
        process.stderr.cursorTo(0);
        process.stderr.write(percent + '% ' + message);
      }
    }),
  ],

  resolveLoader: {
    alias: {
      domReady: '@sdinteractive/requirejs-loader',
      text: 'raw-loader',
    },
  },

  resolve: {
    plugins: [
      new RequireJsResolverPlugin({
        configPath: settings.magentoLocation + REQUIREJS_CHOSEN_CONFIG,
      }),
    ],

    modules: [
      /**
       * We want to try to resolve everything
       * locally if possible, then through
       * static assets, and then finally
       * through NPM packages
       */
      './js',
      path.resolve(settings.magentoLocation, PUBLIC_STATIC_DIR),
      './node_modules',
    ],

    // .min.js is needed for production builds using minify.
    extensions: [".webpack.js", ".web.js", ".js", ".json", ".min.js"],
  },
};
