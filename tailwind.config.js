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
                green: {
                    600: '#277C78',
                },
                red: {
                    600: '#dc2626',
                },
                gray: {
                    900: '#201F24'
                }
            },
        },
        borderRadius: {
            DEFAULT: '12px', // Set the default 'rounded' class to 12px
            sm: '4px',
            md: '8px',
            lg: '16px',
            full: '9999px',
        },
    },

    plugins: [forms],
};
