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
                'arial-black': ['"Arial Black"', '"Arial Bold"', 'Arial', 'sans-serif'],
            },
            colors: {
                'utn-blue': '#008CCC',       // Azul UTN oficial - acento, fondos de botones (3.73:1 en blanco, OK para large/UI)
                'utn-blue-dark': '#006699',   // Azul oscuro - texto sobre blanco, links (6.25:1 AA)
                'utn-blue-darker': '#004D73', // Azul más oscuro - headers, nav (9.09:1 AAA)
                'utn-blue-light': '#33A3D6',  // Azul claro - solo sobre fondos oscuros
                'utn-dark': '#1A1A1A',        // Negro suave - texto principal, nav (17.40:1)
                'utn-dark-light': '#2D2D2D',  // Negro claro - elementos secundarios (13.77:1)
                'utn-dark-lighter': '#404040', // Gris oscuro - bordes, subtexto (10.37:1)
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
