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
        fontSize: {
            xs:    fontVariant(0.75),
            sm:    fontVariant(0.875),
            base:  fontVariant(1),
            lg:    fontVariant(1.125),
            xl:    fontVariant(1.25),
            '2xl': fontVariant(1.5),
        },
        extend: {
            colors: {
                primary: colors.blue,
            },
            fontFamily: {
                'sans': ['Inter var', ...defaultTheme.fontFamily.sans],
                'mono': ['"SF Mono"', ...defaultTheme.fontFamily.mono],
            },
            // make the navbar height constant
            height: {
                'navbar': '4rem',
            },
            // make the sidebar width a constant
            width: {
                'sidebar-lg':  '14rem',
                'sidebar-2xl': '16rem',
                'content-lg':  '48rem',
                'content-xl':  '50rem', // only slightly larger because the toc appears
                'content-2xl': '58rem',
            },
            // fix some border UI bugs on Safari
            borderColor: {
                DEFAULT: 'transparent',
            },
            fontWeight: {
                'inherit': 'inherit',
            },
            screens: {
                // Add print as a screen size despite it already exists ath the TailwindCSS library
                // because the order does not get respected when using this variant.
                'print': {'raw': 'print'},
            },
        },
    },
    plugins: [
        require('flowbite/plugin'),
        require('@tailwindcss/typography'),
        backdropBlur,
        isProse,
        rtl,
    ],
};

function fontVariant(factor) {
    return [`${factor}rem`, `${1.5 * factor}rem`];
}

// adds css query, that checks, if the browser supports backdrop blur
function backdropBlur({ addVariant }) {
    addVariant(
        'supports-backdrop-blur',
        '@supports (backdrop-filter: blur(0)) or (-webkit-backdrop-filter: blur(0))',
    );
}

// adds a variant, that is only used, if the current class is not a child of a 'not-prose' class
function isProse({ addVariant, e }) {
    addVariant(
        'is-prose',
        ({ modifySelectors, separator }) => {
            modifySelectors(({ className }) => `.${e(`is-prose${separator}${className}`)}:not(:where([class~="not-prose"] *))`);
        }
    );
}

// adds a rtl variant
function rtl({ addVariant, e }) {
    addVariant(
        'rtl',
        ({ modifySelectors, separator }) => {
            modifySelectors(({ className }) => `[dir='rtl'] .${e(`rtl${separator}${className}`)}`);
        }
    );
}
