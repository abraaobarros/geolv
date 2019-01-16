let mix = require('laravel-mix');

if (process.env.NODE_ENV === 'production') {
    mix.disableNotifications();
}

mix
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps()
    .version();
