/** @type {import('tailwindcss').Config} */

const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './**/*.php',
        './node_modules/flowbite/**/*.js',
    ],
    darkMode: 'media',
    theme: {
        extend: {
            colors: {
                primary: {
                    "50":  "#eff6ff",
                    "100": "#dbeafe",
                    "200": "#bfdbfe",
                    "300": "#93c5fd",
                    "400": "#60a5fa",
                    "500": "#3b82f6",
                    "600": "#2563eb",
                    "700": "#1d4ed8",
                    "800": "#1e40af",
                    "900": "#1e3a8a",
                    "950": "#172554",
                }
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
