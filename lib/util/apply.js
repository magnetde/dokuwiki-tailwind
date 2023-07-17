const postcssJS = require('postcss-js'); // TODO: remove from package.json, if the apply function is not used anymore
const tailwind = require('tailwindcss');

// Initialize the postcss-js only once
const postcss = postcssJS.sync([
    tailwind({
        content: ['<html />'],
        corePlugins: { preflight: false },
    })
]);

module.exports = (classes, ...vals) => {
    const ROOT = '&';

    // Normalize the class list
    classes = (
        Array.isArray(classes) ?
        String.raw({ raw: classes }, ...vals) :
        classes
    ).replace(/\s+/g, ' ').trim();

    // Input element, that works with TailwindCSS;
    let input = {
        [ROOT]: {
            [`@apply ${classes}`]: {},
        }
    };

    // Apply PostCSS to the dummy element
    const result = postcss(input);

    // If the result contains only the root key, return this element
    if(Object.keys(result).length === 1 && result.hasOwnProperty(ROOT))
        return result[ROOT];

    return result;
};
