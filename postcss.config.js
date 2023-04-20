// postcss.config.js
module.exports = {
    parser: require('postcss-comment'),
    plugins: {
        'postcss-import': {},
        'tailwindcss/nesting': {},
        tailwindcss: {},
        autoprefixer: {},
    }
}
