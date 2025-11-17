import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'utn-blue': '#003366',
                'utn-orange': '#FF6600',
            },
        },
    },
    plugins: [],
    // Optimizaciones para desarrollo
    safelist: [],
    future: {
        hoverOnlyWhenSupported: true,
    },
};
