/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './**/*.php',
        './node_modules/flowbite/**/*.js',
    ],
    darkMode: 'media',
    theme: {
        extend: {
            // add a new default width to increase the content width
            maxWidth: {
                '10xl': '104rem',
            },
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
    );
}
