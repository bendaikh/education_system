import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#f5f7ff',
                    100: '#e8ecff',
                    200: '#c6cdff',
                    300: '#a3afff',
                    400: '#8090ff',
                    500: '#607AFB',
                    600: '#4c61e3',
                    700: '#394ab6',
                    800: '#2d3a8c',
                    900: '#222b66',
                    DEFAULT: '#607AFB',
                },
            },
        },
    },

    plugins: [forms],
};
