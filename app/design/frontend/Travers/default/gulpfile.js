'use strict';

const argv = require('minimist')(process.argv.slice(2));
const gulp = require('gulp');
const concat = require('gulp-concat');
const notify = require('gulp-notify');
const cheerio = require('gulp-cheerio');
const fs = require('fs');
const hologram = require('gulp-hologram');
const rename = require('gulp-rename');
const svgstore = require('gulp-svgstore');
const sourcemaps = require('gulp-sourcemaps');
const util = require('gulp-util');
const webpack = require('webpack-stream');

// CLI options
const enabled = {
  // Enable styleguide compilation on `gulp watch` when `--styleguide`
  styleguide: argv.styleguide
};

/*
    SETTINGS
*/
const settings = {
  themeName: 'Travers/default',
  themeLanguage: ['en_US'], // ['en_US', 'ja_JP']
  // path to the Magento root
  magentoLocation: fs.realpathSync('../../../../../')
};

/*
    PATHS
*/
let paths = {
  theme: process.cwd(),
  snowdog: `${settings.magentoLocation}/vendor/snowdog/frontools`,
};

let sassMapFiles = [
  'fonts/Blank-Theme-Icons/Blank-Theme-Icons.eot',
  'fonts/Blank-Theme-Icons/Blank-Theme-Icons.woff2',
  'fonts/Blank-Theme-Icons/Blank-Theme-Icons.woff',
  'fonts/Blank-Theme-Icons/Blank-Theme-Icons.ttf',
  'fonts/Blank-Theme-Icons/Blank-Theme-Icons.svg',
  'images/loader-2.gif',
  'images/gallery.png',
  'images/loader-1.gif',
  'Magento_Company/images/icon-tree.png',
  'Magento_ProductVideo/img/gallery-sprite.png',
  'Magento_Swatches/images/loader-2.gif',
];

function handleErrors(args) {
  if (util.env.ci) {
    process.stderr.write(args ? (args.error || args) : '(Unknown error)');
    process.exit(1);
  }

  // Send error to notification center with gulp-notify
  notify.onError({
    title: 'Compile Error 🔥',
    message: '<%= error %>'
  }).apply(this, args);

  // Keep gulp from hanging on this task
  this.emit('end');
}

function makePubStatic(lang) {
  return `${settings.magentoLocation}/pub/static/frontend/${settings.themeName}/${lang}`;
}

/*
  ~~~~~~~~~~~~~~~~~~~~~~~ SNOWDOG ~~~~~~~~~~~~~~~~~~~~~~~
*/

/*
    SNOWDOG :: Import tasks
*/
process.chdir(paths.snowdog);
const snowdog = require(`${paths.snowdog}/node_modules/gulp`);
require(`${paths.snowdog}/gulpfile.js`);
process.chdir(paths.theme);

/**
 * Steal the Snowdog tasks so we can run them via argument.
 *
 * This places them into the one in this directory,
 * which is what gulp will find. We make sure to preface
 * each task with "snowdog:" - effectively namespacing them.
 */
for (let k of Object.keys(snowdog.tasks)) {
  gulp.task(k, snowdog.tasks[k].fn);
  gulp.task('snowdog:' + k, snowdog.tasks[k].fn);
  snowdog.tasks[k].name = `snowdog:${snowdog.tasks[k].name}`;
}

/*
  ~~~~~~~~~~~~~~~~~~~~~~~ TASKS ~~~~~~~~~~~~~~~~~~~~~~~
*/

gulp.task('styleguide', () => {
  console.log('Building styleguide... 🎨 📓');

  return gulp.src(`${settings.magentoLocation}/hologram/hologram_config.yml`)
    .pipe(hologram({
      bundler: true,
      logging: true,
    })
  );
});

gulp.task('svg', () => {
  console.log('Combining svgs... 🎏');
  let pipeline = gulp.src(`${paths.theme}/web/svg/**/*.svg`)
  .pipe(svgstore({ inlineSvg: true }))
  .pipe(cheerio(function($) {
      $('[fill]').removeAttr('fill');
      $('svg').removeAttr('width');
      $('svg').removeAttr('height');
      $('style').remove();
      $('svg').addClass('svg-map');
      $('svg').attr('focusable', false);

      if (!$('symbol').attr('preserveAspectRatio')) {
          $('symbol').attr('preserveAspectRatio', 'xMidYMid meet');
      }
  }))
  .pipe(rename('symbols.svg'));

  settings.themeLanguage.forEach(lang => {
    pipeline = pipeline.pipe(gulp.dest(
      `${makePubStatic(lang)}/svg`
    ));
  });

  return pipeline;
});

/**
 * You should normally only be running `gulp scripts`
 *
 * Webpack outputs partials, but we're only loading in
 * the concatenated file
 */
gulp.task('webpack', () => {
  let pipeline = gulp.src('./webpack.config.js')
    .pipe(webpack(require('./webpack.config.js'), require('webpack')))
    .on('error', handleErrors)

  settings.themeLanguage.forEach(lang => {
    pipeline = pipeline.pipe(gulp.dest(
      `${makePubStatic(lang)}/js/dist`
    ));
  });

  return pipeline;
});

/**
 * Run webpack and concatenate resulting script
 * partials
 */
gulp.task('scripts:partials', gulp.series('webpack', function combineCommonJs() {
  const sourceLang = makePubStatic(settings.themeLanguage[0]);
  let pipeline = gulp.src([
    `${sourceLang}/js/dist/common-partial.js`,
    `${sourceLang}/js/dist/global.js`,
  ])
    .pipe(sourcemaps.init({ loadMaps: true }))
    .pipe(concat('common.js'))
    .pipe(sourcemaps.write('.'))

  settings.themeLanguage.forEach(lang => {
    pipeline = pipeline.pipe(gulp.dest(
      `${makePubStatic(lang)}/js/dist`
    ));
  });

  return pipeline;
}));

gulp.task('scripts', gulp.series('scripts:partials', function generateMinJs() {
  // Copy to .min suffixes as well.
  const sourceLang = makePubStatic(settings.themeLanguage[0]);
  let pipeline = gulp.src(`${sourceLang}/js/dist/\*.js`)
    .pipe(rename(path => {
      if (path.basename.substr(-4) !== '.min') {
        path.basename += '.min';
      }
  }));

  settings.themeLanguage.forEach(lang => {
    pipeline = pipeline.pipe(gulp.dest(
      `${makePubStatic(lang)}/js/dist`
    ));
  });

  return pipeline;
}));

function generateSassMap(mapping) {
  return Promise.resolve(null).then(() => {
    let template = `// This file is generated by gulp.  Do not commit.\n$map-paths: (\n`;
    for (let key in mapping) {
      template += `  ${JSON.stringify(key)}: ${JSON.stringify(mapping[key])},\n`;
    }
    template += `);\n`;

    return template;
  }).then(template => {
    let promise = Promise.resolve(null);
    fs.writeFile(`${paths.theme}/styles/_map-paths.scss`, template, (err) => {
      // The result is passed to the gulp callback either way.
      return err;
    });
    return promise;
  });

}

function generateDefaultSassMap() {
  return Promise.resolve(null).then(() => {
    let mapping = {};
    for (let file of sassMapFiles) {
      mapping[file] = '../' + file;
    }
    return generateSassMap(mapping);
  });
}

function generateCompactSassMap(mapJson) {
  return Promise.resolve(null).then(() => {
    let mapping = {};
    for (let file of sassMapFiles) {
      let entry;
      if (file in mapJson) {
        entry = mapJson[file];
      } else if (file.replace('/', '::') in mapJson) {
        entry = mapJson[file.replace('/', '::')];
      }

      if (!entry) {
        console.warn('WARNING: Unmapped sass file:', file);
        mapping[file] = '../' + file;
      } else {
        // Need enough ..s to get out of the css path.
        mapping[file] = `../../../../../${entry.area}/${entry.theme}/${entry.locale}/${file}`;
      }
    }

    return generateSassMap(mapping);
  });
}

gulp.task('styles:prep', (done) => {
  const sourceLang = makePubStatic(settings.themeLanguage[0]);
  const mapJsonFile = `${sourceLang}/map.json`;
  fs.access(mapJsonFile, (err) => {
    if (err) {
      generateDefaultSassMap().then(done, done);
    } else {
      generateCompactSassMap(require(mapJsonFile)).then(done, done);
    }
  });
});

gulp.task('styles', gulp.series('styles:prep', 'snowdog:styles'));

gulp.task('watch', gulp.parallel('snowdog:browser-sync', function watch() {
  let styles;
  if (enabled.styleguide) {
    styles = gulp.parallel('styles', 'styleguide');
  } else {
    styles = gulp.parallel('styles');
  }
  gulp.watch([`${paths.theme}/**/*.scss`, `!${paths.theme}/styles/_map-paths.scss`], styles);
  gulp.watch(`${paths.theme}/**/*.svg`, gulp.parallel('svg'));
  gulp.watch(`${paths.theme}/js/**/*.js`, gulp.parallel('scripts'));
}));

/**
 * Execute all the build tasks
 */
const build = gulp.parallel('styles', 'styleguide', 'svg', 'scripts');
gulp.task('build', build);
gulp.task('default', build);

/**
 * Production deployment task
 *
 * USAGE :: gulp production --prod --ci
 */
gulp.task('production', build);
