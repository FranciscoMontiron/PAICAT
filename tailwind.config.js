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
                sans: ['Arial', 'Helvetica', ...defaultTheme.fontFamily.sans],
                arial: ['Arial', 'Helvetica', 'sans-serif'],
            },
            colors: {
                'utn-blue': '#003366',
                'utn-blue-dark': '#002244',
                'utn-blue-light': '#004488',
                'utn-orange': '#FF6600',
                'utn-orange-dark': '#E55A00',
                'utn-orange-light': '#FF8533',
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
