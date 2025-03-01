const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .react()
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
    ]);

// Copy the compiled assets to the public directory when in production
if (mix.inProduction()) {
    mix.version();
} 