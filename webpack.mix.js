let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.styles([
    'resources/assets/css/bootstrap.min.css'
], 'public/css/bootstrap.min.css');

mix.styles([
    'resources/assets/css/jquery-ui.min.css'
], 'public/css/jquery-ui.min.css');

mix.styles([
    'resources/assets/css/jquery-ui-timepicker-addon.css'
], 'public/css/jquery-ui-timepicker-addon.css');


mix.js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/jquery.slimscroll.js', 'public/js')
    .less('resources/assets/less/app.less', 'public/css');
