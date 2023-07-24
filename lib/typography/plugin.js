const mergeDeep = require('../util/mergedeep');

// With subelements are necessary to format the specific element.
const subElements = {
    h1: [],
    h2: [],
    h3: [],
    h4: [],
    h5: [],
    p: [], // lightweight paragraph
    a: ['a'],
    ul: ['li'],
    table: ['thead', 'tbody', 'tfoot'],
    text: {
        self: 'p', // prose-text should be used on p elements
        sub: ['strong', 'code', 'a'], // does not contain many formattings; add if needed
    },
};

const defaultSelectors = ['^--tw-', '^color$', '^fontSize$', '^lineHeight$'];

// Handles the dynamic `prose-x` components.
const prosePlugin = ({ theme, matchComponents }) => {
    const typography = theme('typography');
    const typographyDefault = typography.DEFAULT.css;
    const typographySm = typography.sm.css;

    const values = Object.keys(subElements).reduce((prev, key) => (prev[key] = key, prev), {});

    matchComponents(
        {
            'prose': (value) => {
                let self = value;
                let sub = subElements[value];

                if(!Array.isArray(sub)) {
                    self = sub.self;
                    sub = sub.sub;
                }

                const selectors = regexp([
                    elementRegexp(self),
                    ...sub.map(elementRegexp),
                    ...defaultSelectors,
                ]);

                return [
                    pickStyles(typographyDefault, selectors, self),
                    pickStyles(typographySm, selectors, self),
                ]
            },
        },
        { values }
    );
};

// Transforms the element type into a regexp, that matches the selectors, that start with the element type.
const elementRegexp = (elm) => `^${elm}([^a-z0-9]|$)`;

// Joins the list of regexps into a single regexp, that matches at least one of the regexps.
const regexp = (values) => new RegExp(values.map(v => `(${v})`).join('|'))

// Picks all styles at the list of styles, that matches the selector regexp and replaces the element type with '&' symbols.
const pickStyles = (styles, selectors, replace) => {
    let res = styles.reduce((prev, obj) => {
        const subobj = Object.keys(obj)
            .filter(key => testSelector(key, selectors))
            .reduce((prev, key) => (prev[key] = obj[key], prev), {});

        return mergeDeep(prev, subobj);;
    }, {});

    return replaceElement(res, replace);
};

// Tests, if one of the selector parts matches the regexp.
const testSelector = (selector, regexp) => (
    splitSelector(selector)
        .filter(part => regexp.test(part))
        .length > 0
);

// Splits a selector into multiple selector parts
const splitSelector = (selector) => selector.split(',').map(part => part.trim());

// Replaces the element type in all selectors in the style object with the '&' symbol.
const replaceElement = (styles, element) => {
    const regexp = new RegExp(elementRegexp(element));

    return Object.keys(styles)
        .reduce((prev, key) => (
            prev[
            splitSelector(key)
                .map(part => part.replace(regexp, '& ').trim())
                .join(', ')
            ] = styles[key],
            prev), {});
};

module.exports = prosePlugin;
