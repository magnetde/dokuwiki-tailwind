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
                navbar: '4.5rem',
            },
            // make the sidebar width a constant
            width: {
                'sidebar-lg':  '16rem',
                'sidebar-2xl': '18rem',
                'content-lg':  '56rem',
                'content-xl':  '50rem', // smaller because the table of content appears
                'content-2xl': '66rem',
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

function backdropBlur({ addVariant }) {
    addVariant(
        'supports-backdrop-blur',
        '@supports (backdrop-filter: blur(0)) or (-webkit-backdrop-filter: blur(0))',
    )
}
