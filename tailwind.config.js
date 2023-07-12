const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            'white': '#ffffff',
            'midnight': '#000000',
            'primary': '#157547',
            'secondary': '#a1cd3a',
            'danger': '#ff0000ff',
          },
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [require('@tailwindcss/forms'), require('flowbite/plugin')],

};


