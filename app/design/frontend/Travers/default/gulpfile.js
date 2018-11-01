'use strict';

const argv = require('minimist')(process.argv.slice(2));
const gulp = require('gulp');
const gulpSequence = require('gulp-sequence');
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
  publicStatic: `${settings.magentoLocation}/pub/static/frontend/${settings.themeName}/${settings.themeLanguage[0]}`,
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
    process.stderr.write(args.error);
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

/*
  ~~~~~~~~~~~~~~~~~~~~~~~ SNOWDOG ~~~~~~~~~~~~~~~~~~~~~~~
*/

/*
    SNOWDOG :: Import tasks
*/
process.chdir(paths.snowdog);
const snowdog = require(`${paths.snowdog}/node_modules/gulp`);
require(`${paths.snowdog}/gulpfile.js`);

/**
 * Adds unprefaced snowdog tasks to current gulp tasks
 * to maintain compatability with older setups.
 */
gulp.tasks = snowdog.tasks;

/**
 * Steal the Snowdog tasks so we can run them via argument.
 *
 * This places them into the one in this directory,
 * which is what gulp will find. We make sure to preface
 * each task with "snowdog:" - effectively namespacing them.
 */
for (let k of Object.keys(snowdog.tasks)) {
  gulp.tasks['snowdog:' + k] = snowdog.tasks[k];
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
  return gulp.src(`${paths.theme}/web/svg/**/*.svg`)
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
  .pipe(rename('symbols.svg'))
  .pipe(gulp.dest(`${paths.publicStatic}/svg/`));
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
      `${settings.magentoLocation}/pub/static/frontend/${settings.themeName}/${lang}/js/dist`
    ));
  });

  return pipeline;
});

/**
 * Run webpack and concatenate resulting script
 * partials
 */
gulp.task('scripts:partials', ['webpack'], () => {
  let pipeline = gulp.src([
    `${paths.publicStatic}/js/dist/common-partial.js`,
    `${paths.publicStatic}/js/dist/global.js`,
  ])
    .pipe(sourcemaps.init({ loadMaps: true }))
    .pipe(concat('common.js'))
    .pipe(sourcemaps.write('.'))

  settings.themeLanguage.forEach(lang => {
    pipeline = pipeline.pipe(gulp.dest(
      `${settings.magentoLocation}/pub/static/frontend/${settings.themeName}/${lang}/js/dist`
    ));
  });

  return pipeline;
});

gulp.task('scripts', ['scripts:partials'], () => {
  // Copy to .min suffixes as well.
  let pipeline = gulp.src(`${paths.publicStatic}/js/dist/*.js`)
    .pipe(rename(path => {
      if (path.basename.substr(-4) !== ".min") {
        path.basename += ".min";
      }
  }));

  settings.themeLanguage.forEach(lang => {
    pipeline = pipeline.pipe(gulp.dest(
      `${settings.magentoLocation}/pub/static/frontend/${settings.themeName}/${lang}/js/dist`
    ));
  });

  return pipeline;
});

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
  const mapJsonFile = `${paths.publicStatic}/map.json`;
  fs.access(mapJsonFile, (err) => {
    if (err) {
      generateDefaultSassMap().then(done, done);
    } else {
      generateCompactSassMap(require(mapJsonFile)).then(done, done);
    }
  });
});

gulp.task("styles", done => {
  gulpSequence("styles:prep", "snowdog:styles")(done);
});

gulp.task('watch', ['snowdog:browser-sync'], () => {
  if (enabled.styleguide) {
    gulp.watch(`${paths.theme}/**/*.scss`, ['styles', 'styleguide']);
  } else {
    gulp.watch(`${paths.theme}/**/*.scss`, ['styles']);
  }
  gulp.watch(`${paths.theme}/**/*.svg`, ['svg']);
  gulp.watch(`${paths.theme}/js/**/*.js`, ['scripts']);
});

/**
 * Execute all the build tasks
 */
gulp.task('build', ['styles', 'styleguide', 'svg', 'scripts']);

gulp.task('default', ['build']);


 /**
  * Production deployment task
  *
  * USAGE :: gulp production --prod --ci
  */

// Ensure tasks are run sequentially since gulp v3 does not
let tasksLength = 0;
let tasksCounter = 0;

function runSequential(tasks) {
  if (!tasks || tasks.length <= 0) return;

  tasksCounter++;

  if (tasksLength == 0) {
    tasksLength = tasks.length;
  }

  const task = tasks[0];
  gulp.start(task, () => {
    console.log(`✅  ${task} finished.  (Progress: ${tasksCounter}/${tasksLength})`);
    runSequential(tasks.slice(1)); // drop current task (has an index of 0)
  });
}

gulp.task('production', () => runSequential(['svg', 'styleguide', 'styles', 'scripts', 'deploy']));
