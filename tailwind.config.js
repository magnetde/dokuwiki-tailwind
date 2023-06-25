/** @type {import('tailwindcss').Config} */

const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

module.exports = {
    content: [
        './**/*.php',
        './node_modules/flowbite/**/*.js',
    ],
    darkMode: 'media',
    theme: {
        extend: {
            colors: {
                primary: colors.blue,
            },
            fontFamily: {
                'sans': ['Inter', ...defaultTheme.fontFamily.sans],
                'mono': ['"Roboto Mono"', ...defaultTheme.fontFamily.mono],
            },
            // make the navbar height constant
            height: {
                'navbar': '4.5rem',
            },
            // make the sidebar width a constant
            width: {
                'sidebar-lg':  '16rem',
                'sidebar-2xl': '18rem',
                'content-lg':  '54rem',
                'content-xl':  '56rem', // only slightly larger because the toc appears
                'content-2xl': '66rem',
            },
            // fix some border UI bugs on Safari
            borderColor: {
                DEFAULT: 'transparent',
            },
            fontWeight: {
                'inherit': 'inherit',
            },
            screens: {
                // add print as a screen size despite it already exists because the order does not get respected
                'print': {'raw': 'print'},
            },
        },
    },
    plugins: [
        require('flowbite/plugin'),
        require('@tailwindcss/typography'),
        backdropBlur,
        rtl,
    ],
};

function backdropBlur({ addVariant }) {
    addVariant(
        'supports-backdrop-blur',
        '@supports (backdrop-filter: blur(0)) or (-webkit-backdrop-filter: blur(0))',
    );
}

function rtl({ addVariant, e }) {
    addVariant(
        'rtl',
        ({ modifySelectors, separator }) => {
            modifySelectors(({ className }) => `[dir='rtl'] .${e(`rtl${separator}${className}`)}`);
        }
    );
}
