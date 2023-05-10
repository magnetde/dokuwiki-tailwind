/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './**/*.php',
        './node_modules/flowbite/**/*.js',
    ],
    darkMode: 'media',
    theme: {
        extend: {
            // make the navbar height constant
            height: {
                navbar: navbarHeight(),
                content: contentHeight(),
            },
            minHeight: {
                content: contentHeight(),
            },
            // add a new default width to increase the content width
            maxWidth: {
                '10xl': '104rem',
            },
            // fix some border UI bugs on Safari
            borderColor: {
                DEFAULT: 'transparent',
            }
        },
    },
    plugins: [
        require('flowbite/plugin'),
        require('@tailwindcss/typography'),
        backdropBlur,
    ],
}

function navbarHeight() {
    return '4.5rem'
}

function contentHeight() {
    return `calc(100vh - ${navbarHeight()} - 2px)`
}

function backdropBlur({ addVariant }) {
    addVariant(
        'supports-backdrop-blur',
        '@supports (backdrop-filter: blur(0)) or (-webkit-backdrop-filter: blur(0))',
    )
}
