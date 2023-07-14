/**
 * 
 */

const apply = require('./apply');
const dark = require('./dark');

const { link, dropdownContainer } = require('./components');

// Extensions for the typography plugin
// TODO: using the apply syntax inside an JavaScript file makes the code shorter but MUCH more unreadable.
// This variant was used to directy apply my CSS styles inside this theme extension.
// In the future, it would be better to just use the CSS-in-JS styles directly.
module.exports = (theme) => ({
    DEFAULT: {
        css: {
            '--tw-prose-links': theme('colors.primary.700'),
            '--tw-prose-code': theme('colors.primary.700'),
            '--tw-prose-code-bg': theme('colors.primary.50'),
            '--tw-prose-dl-dt-bg': `${theme('colors.gray.800/95')}`,
            '--tw-prose-dl-dt-border': theme('colors.gray.700'),
            '--tw-prose-invert-links': theme('colors.primary.500'),
            '--tw-prose-invert-code': theme('colors.primary.400'),
            '--tw-prose-invert-code-bg': theme('colors.primary.950'),
            '--tw-prose-invert-dl-dt-bg': theme('colors.gray.950/50'),
            '--tw-prose-invert-dl-dt-border': theme('colors.gray.800'),

            h5: apply`font-semibold`,
            p: apply`first:mt-0`,
            ul: apply`my-2`,
            li: apply`my-1`,
            // Underline text
            'em.u': apply`not-italic underline`,
            // Footnotes (in text)
            'a.fn_top': link('--tw-prose-links'),
            a: {
                // Regular links
                '&.wikilink1, &.urlextern, &.mail, &.windows, &.mediafile, &.interwiki': {
                    ...link('--tw-prose-links'),
                    ...apply`[font-weight:inherit]`,
                },
                // Dead links
                '&.wikilink2': {
                    ...link(theme('colors.red.700'), theme('colors.red.500')), // TODO: via invert variables,
                    ...apply`[font-weight:inherit]`,
                },
                // Special links with icons
                '&.urlextern, &.mail, &.windows, &.mediafile, &.interwiki': apply`py-0 pr-0 pl-[1.4rem] bg-no-repeat bg-left bg-contain`,
                ...linkIcons({
                    '&.urlextern': 'link-external',
                    '&.mail': 'email',
                    '&.windows': 'windows-share'
                }),
                '&.interwiki': {
                    ...linkIcons({
                        '&.iw_amazon, &.iw_amazon_de, &.iw_amazon_uk': 'amazon',
                        '&.iw_callto, &.iw_tel': 'iw_tel',
                        '&.iw_go, &.iw_google': 'google',
                        '&.iw_paypal': 'paypal',
                        '&.iw_phpfn': 'php',
                        '&.iw_rfc': 'rfc',
                        '&.iw_skype': 'skype',
                        '&.iw_this': 'rss',
                        '&.iw_user': 'user',
                        '&.iw_man': 'cmd',
                        '&.iw_wp, &.iw_wpde, &.iw_wpes, &.iw_wpfr, &.iw_wpjp, &.iw_wpmeta, &.iw_wppl': 'wikipedia',
                    }),
                    '&.iw_wp, &.iw_wpde, &.iw_wpes, &.iw_wpfr, &.iw_wpjp, &.iw_wpmeta, &.iw_wppl': {
                        // icons are gray-900 / white because the Wikipedia logo is too thin
                        ...apply`bg-[url('/lib/tpl/tailwind/icon.php?icon=wikipedia&color=gray-900')]`,
                        ...dark(apply`bg-[url('/lib/tpl/tailwind/icon.php?icon=wikipedia&color=white')]`)
                    },
                },
                '&.iw_doku': apply`bg-[url('/lib/images/interwiki/doku.svg')]`,
            },
            // Code
            code: apply`p-0.5 rounded text-[--tw-prose-code] bg-[--tw-prose-code-bg]`,
            pre: apply`rounded-lg`,
            'dl.code, dl.file': {
                ...apply`mt-6 mb-7`,
                dt: {
                    ...apply`
                        pl-4 pr-3 py-2.5 flex flex-nowrap items-center justify-between
                        rounded-t-lg border-b space-x-4
                        bg-[--tw-prose-dl-dt-bg] border-[--tw-prose-dl-dt-border]
                    `,
                    '> span': apply`text-sm font-semibold truncate text-white`,
                    '> a': apply`text-gray-200`,
                },
                dd: {
                    ...apply`p-0 rounded-b-lg`,
                    'pre.code, pre.file': apply`m-0 rounded-t-none rounded-b-lg`,
                },
            },
            '.icon.smiley': apply`inline h-[1.2rem] m-0`,
            // Footnotes (at the bottom)
            '.footnotes': {
                a: {
                    ...link('--tw-prose-links'),
                    ...apply`[font-weight:inherit]`,
                },
                '.content': apply`inline`,
            },
            // Section headers
            '.section-header': {
                ...apply`flex flex-nowrap items-baseline`,
                '.anchor': apply`
                    ml-1 no-underline opacity-0 transition-opacity
                    text-gray-300 hover:text-gray-400
                    dark:text-gray-500 dark:hover:text-gray-400
                `,
                '.secedit': {
                    ...apply`ml-auto opacity-0`,
                    '.btn_secedit': {
                        ...apply`inline-block`,
                        button: {
                            ...apply`
                                bg-no-repeat bg-contain bg-center
                                bg-[url('/lib/tpl/tailwind/icon.php?icon=edit&color=gray-300')]
                                hover:bg-[url('/lib/tpl/tailwind/icon.php?icon=edit&color=gray-400')]
                                dark:bg-[url('/lib/tpl/tailwind/icon.php?icon=edit&color=gray-500')]
                                dark:hover:bg-[url('/lib/tpl/tailwind/icon.php?icon=edit&color=gray-400')]
                            `,
                            '&:where(h1 *)': apply`w-7 h-7`,
                            '&:where(h2 *)': apply`w-6 h-6`,
                            '&:where(h3 *)': apply`w-5 h-5`,
                            '&:where(h4 *)': apply`w-4 h-4`,
                        },
                    },
                },
                '&:hover': {
                    '.anchor, .secedit': apply`opacity-100`,
                },
            },
            'div.insitu-footnote': {
                ...dropdownContainer,
                ...apply`text-sm p-3 w-fit max-w-[40%]`
            },
            // Minor design fix
            ...listFix,
        },
    },
    // Extend invert (for dark mode)
    invert: {
        css: {
            '--tw-prose-code-bg': 'var(--tw-prose-invert-code-bg)',
            '--tw-prose-dl-dt-bg': 'var(--tw-prose-invert-dl-dt-bg)',
            '--tw-prose-dl-dt-border': 'var(--tw-prose-invert-dl-dt-border)',
        },
    },
    // Sidebar fix
    sm: {
        css: {
            ...listFix,
        }
    },

    // TODO: typography-p
});

const linkIcons = (icons) => Object.fromEntries(
    Object.entries(icons).map(
        ([k, v]) => [k, {
            backgroundImage: `url('/lib/tpl/tailwind/icon.php?icon=${v}&color=gray-500')`,
            ...dark({ backgroundImage: `url('/lib/tpl/tailwind/icon.php?icon=${v}&color=gray-400')` })
        }
    ])
);

// Fix for lists, used multiple times and defined here
const listFix = {
    '>': {
        'ul, ol': {
            '> li > *': {
                '&:first-child': apply`mt-0`,
                '&:last-child': apply`mb-0`,
            },
        },
    },
};
