const mix = require('laravel-mix');

mix.setPublicPath('./');

mix.postCss('./source/css/mu-hr-training.css', 'css/mu-hr-training.css', [
    require('postcss-import'),
    require('postcss-nesting'),
    require('tailwindcss'),
		require('autoprefixer')
  ]
);

if (mix.inProduction()) {
    mix.version();
}
