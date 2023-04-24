// postcss.config.js
module.exports = {
    parser: 'postcss-scss',
    plugins: {
        'postcss-import': {},
        'tailwindcss/nesting': {},
        'tailwindcss': {},
        'autoprefixer': {},
    }
}
