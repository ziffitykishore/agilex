// Theme Gulpfile Path
var PATH = process.cwd() + '/vendor/somethingdigital/magento2-theme-bryantpark';

process.chdir(PATH);

var gulp = require('gulp');
var real = require(__dirname + '/node_modules/gulp');
require(PATH + '/gulpfile.js');

// Steal the tasks so we can run them via argument.
// This places them into the one in this directory, which is what "gulp" will find.
gulp.tasks = real.tasks;
