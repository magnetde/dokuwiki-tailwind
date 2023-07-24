const plugin = require('tailwindcss/plugin');

const {
    link,
    dropdownContainer,
    dropdownElement,
} = require('.');

module.exports = plugin(({ addComponents, theme }) => {
    addComponents({
        '.link-primary': link(theme('colors.primary.700'), theme('colors.primary.500')),
        '.link-text': link(theme('colors.gray.900'), theme('colors.white')),
        '.link-error': link(theme('colors.red.700'), theme('colors.red.500')),
        '.dropdown-container': dropdownContainer,
        '.dropdown-element': dropdownElement,
    });
});
