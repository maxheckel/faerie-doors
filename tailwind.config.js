const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {

            fontSize: {
                'md': '1.2rem',
                'sm': '1rem',
                'large': '1.9rem' /* new custom font size */
            },
            fontFamily: {
                sans: ['Lancelot', ...defaultTheme.fontFamily.sans],
                serif: ['Almendra', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
