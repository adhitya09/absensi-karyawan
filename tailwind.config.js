import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#5046e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                },
                canvas: {
                    light: '#F4F6FC',
                    dark: '#0B0F19',
                    card: '#FFFFFF',
                    cardDark: '#161F30',
                },
            },
            boxShadow: {
                'brand': '0 10px 25px -3px rgba(80, 70, 229, 0.4), 0 4px 6px -2px rgba(80, 70, 229, 0.2)',
                'emerald-glow': '0 10px 25px -3px rgba(16, 185, 129, 0.35), 0 4px 6px -2px rgba(16, 185, 129, 0.2)',
                'soft': '0 8px 30px rgba(0, 0, 0, 0.04)',
                'soft-lg': '0 14px 40px rgba(0, 0, 0, 0.06)',
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
                '4xl': '2rem',
            },
        },
    },

    plugins: [forms, typography],
};
