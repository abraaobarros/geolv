let mix = require('laravel-mix');

if (process.env.NODE_ENV === 'production') {
    mix.disableNotifications();
}

mix
    .react('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .version();
