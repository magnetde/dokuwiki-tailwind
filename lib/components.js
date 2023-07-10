const plugin = require('tailwindcss/plugin')

const apply = require('./apply');

const link = (color, dark) => (
    apply`font-medium no-underline decoration-current hover:underline text-[${color}] ${dark ? `dark:text-[${dark}]` : ''}`
);

const dropdownContainer = apply`
    z-10 hidden divide-y rounded-md focus:outline-none
    bg-white divide-gray-100 ring-black ring-opacity-5 ring-1 shadow-lg
    dark:bg-gray-700 dark:divide-gray-600 dark:ring-0 dark:shadow-none
`;

const dropdownElement = apply`
    block px-4 py-2 w-full text-sm
    text-gray-700 hover:text-gray-900 hover:bg-gray-100
    dark:text-gray-200 dark:hover:text-white dark:hover:bg-gray-600
`;

module.exports = {
    link,
    dropdownContainer,
    plugin: plugin(({ addComponents, theme }) => {
        addComponents({
            '.link-primary': link(theme('colors.primary.700'), theme('colors.primary.500')),
            '.link-text': link(theme('colors.gray.900'), theme('colors.white')),
            '.link-error': link(theme('colors.red.700'), theme('colors.red.500')),
            '.dropdown-container': dropdownContainer,
            '.dropdown-element': dropdownElement,
        });
    }),
}
