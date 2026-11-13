import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#F4F3FE',
                    100: '#ECEBFC',
                    200: '#DEDBFA',
                    500: '#4B3FCB',
                    600: '#3D32B6',
                    700: '#2B2670',
                    800: '#211D56',
                    900: '#1B1D3A',
                },
                saku: {
                    bg: '#E4E7F1',
                    sidebar: '#F7F8FD',
                    card: '#FAFAFE',
                    border: '#ECEEF7',
                    text: '#1B1D3A',
                    muted: '#5B5F80',
                    orange: '#F26B4B',
                    green: '#12A07A',
                    yellow: '#E0A100',
                    red: '#D6372F',
                },
            },
            borderRadius: {
                '2xl': '20px',
                '3xl': '24px',
                '4xl': '28px',
            },
            boxShadow: {
                card: '0 14px 30px rgba(75, 63, 203, 0.10)',
                app: '0 24px 60px rgba(40, 44, 90, 0.10)',
            },
        },
    },

    plugins: [forms],
};
