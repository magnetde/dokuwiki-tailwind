const plugin = require('tailwindcss/plugin')

const apply = require('./apply');

const link = (color, dark) => (
    apply`font-medium no-underline decoration-current hover:underline text-[${color}] ${dark ? `dark:text-[${dark}]` : ''}`
);

module.exports = {
    link,
    plugin: plugin(({ addComponents, theme }) => {
        addComponents({
            '.link-primary': link(theme('colors.primary.700'), theme('colors.primary.500')),
            '.link-text': link(theme('colors.gray.900'), theme('colors.white')),
            '.link-error': link(theme('colors.red.700'), theme('colors.red.500')),
        });
    }),
}
