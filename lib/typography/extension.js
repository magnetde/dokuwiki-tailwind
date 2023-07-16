const apply = require('../apply');
const dark = require('../dark');

const { link, dropdownContainer } = require('../components');

// Extensions for the typography plugin
const proseExtension = (theme) => ({
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

            ...apply`w-full max-w-none`,

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
                '&.urlextern, &.mail, &.windows, &.mediafile, &.interwiki': apply`py-0 pr-0 pl-[1.4em] bg-no-repeat bg-left bg-contain`,
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
                        ...apply`dark:bg-[url('/lib/tpl/tailwind/icon.php?icon=wikipedia&color=white')]`,
                    },
                },
                '&.iw_doku': apply`bg-[url('/lib/images/interwiki/doku.svg')]`,
                '&.mediafile': {
                    ...apply`bg-[url(/lib/images/fileicons/svg/file.svg)]`,
                    ...mediaIcons({
                        mf_7z: '7z.svg',
                        mf_asm: 'asm',
                        mf_bash: 'bash',
                        mf_bz2: 'bz2',
                        mf_c: 'c',
                        mf_conf: 'conf',
                        mf_cpp: 'cpp',
                        mf_cs: 'cs',
                        mf_csh: 'csh',
                        mf_css: 'css',
                        mf_csv: 'csv',
                        mf_deb: 'deb',
                        mf_doc: 'doc',
                        mf_docx: 'docx',
                        mf_file: 'file',
                        mf_gif: 'gif',
                        mf_gz: 'gz',
                        mf_h: 'h',
                        mf_htm: 'htm',
                        mf_html: 'html',
                        mf_ico: 'ico',
                        mf_java: 'java',
                        mf_jpeg: 'jpeg',
                        mf_jpg: 'jpg',
                        mf_js: 'js',
                        mf_json: 'json',
                        mf_lua: 'lua',
                        mf_mp3: 'mp3',
                        mf_mp4: 'mp4',
                        mf_ods: 'ods',
                        mf_odt: 'odt',
                        mf_ogg: 'ogg',
                        mf_ogv: 'ogv',
                        mf_pdf: 'pdf',
                        mf_php: 'php',
                        mf_pl: 'pl',
                        mf_png: 'png',
                        mf_ppt: 'ppt',
                        mf_pptx: 'pptx',
                        mf_ps: 'ps',
                        mf_py: 'py',
                        mf_rar: 'rar',
                        mf_rb: 'rb',
                        mf_rpm: 'rpm',
                        mf_rtf: 'rtf',
                        mf_sh: 'sh',
                        mf_sql: 'sql',
                        mf_svg: 'svg',
                        mf_swf: 'swf',
                        mf_tar: 'tar',
                        mf_tgz: 'tgz',
                        mf_txt: 'txt',
                        mf_wav: 'wav',
                        mf_webm: 'webm',
                        mf_xls: 'xls',
                        mf_xlsx: 'xlsx',
                        mf_xml: 'xml',
                        mf_zip: 'zip',
                    }),
                }
            },
            // Code
            code: apply`p-0.5 rounded text-[--tw-prose-code] bg-[--tw-prose-code-bg]`,
            pre: {
                ...apply`rounded-lg`,
                '&.code, &.file': {
                    // Links
                    a: apply`no-underline`,

                    // Keywords
                    '.kw1, .kw2, .kw4, .kw5, .kw6, .kw7, .kw8, .kw9, .kw10,, .kw11, .kw12, .kw13, .kw14, .kw15, .kw16': apply`text-pink-400 font-semibold`,

                    // attributes in html
                    '.kw3': apply`text-slate-300`,

                    // Comments
                    '.co0, .co1, .co2, .co3, .co4, .coMULTI': apply`text-gray-400 italic`,

                    // Bracket
                    '.br0': apply`text-gray-500`,

                    // Escape chars
                    '.es0, .es1, .es2, .es3, .es4, .es5, .es6, .esHARD': apply`text-rose-400`,

                    // Strings
                    '.st0, .st_h': apply`text-rose-400`,

                    // Numbers
                    '.nu0': apply`text-blue-400`,

                    // Methods
                    '.me0, .me1, .me2': apply`text-violet-400`,

                    // Symbols
                    '.sy0, .sy1, .sy2, .sy3, .sy4': apply`text-gray-500`,

                    // Specials elements
                    '.re0, .re1, .re2, .re3, .re4, .re5, .re6': apply`text-inherit`,

                    // Diff
                    '.re7': apply`text-red-400`,
                    '.re8': apply`text-green-400`,

                    // Scripts
                    '.sc-1': apply`text-gray-300`,
                    '.sc-2': apply`text-gray-400`,
                }
            },
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
});

const linkIcons = (icons) => Object.fromEntries(
    Object.entries(icons).map(
        ([k, v]) => [k, {
            backgroundImage: `url('/lib/tpl/tailwind/icon.php?icon=${v}&color=gray-500')`,
            ...dark({ backgroundImage: `url('/lib/tpl/tailwind/icon.php?icon=${v}&color=gray-400')` })
        }
    ])
);

const mediaIcons = (icons) => Object.fromEntries(
    Object.entries(icons).map(
        ([k, v]) => [`&.${k}`, {
            backgroundImage: `url(/lib/images/fileicons/svg/${v}.svg)`,
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

module.exports = proseExtension;
