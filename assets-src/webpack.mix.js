let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

mix.js('js/app.js', '../assets/cadunico/')
   .sass('sass/app.scss', '../assets/cadunico/')
   .setPublicPath('../assets/cadunico/');
