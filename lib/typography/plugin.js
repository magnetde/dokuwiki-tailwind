module.exports = ({ theme, matchComponents }) => {
    const typography = theme('typography');
    const typographyDefault = typography.DEFAULT.css;
    const typographySm = typography.sm.css;

    const subSelectors = {
        h1: [],
        h2: [],
        h3: [],
        h4: [],
        h5: [],
        p: ['^strong$', '^code$', '^a($|\.)'], // does not contain many formattings; add if needed
        a: ['^a($|\.)'],
        ul: ['^li'],
        table: ['^thead', '^tbody', '^tfoot'],
    };

    const defaultSelectors = ['^--tw-', '^color$', '^fontSize$', '^lineHeight$'];

    const values = Object.keys(subSelectors).reduce((prev, key) => (prev[key] = key, prev), {});

    matchComponents(
        {
            'prose': (value) => {
                const selectors = regexp([`^${value}([^a-z0-9]|$)`, ...subSelectors[value], ...defaultSelectors]);

                return [
                    pickStyles(typographyDefault, selectors, value),
                    pickStyles(typographySm, selectors, value),
                ]
            },
        },
        { values }
    );
};

const regexp = (values) => new RegExp(values.map(v => `(${v})`).join('|'))

const pickStyles = (typography, selectors, replace) => {
    let res = typography.reduce((prev, obj) => {
        const subobj = Object.keys(obj)
            .filter(key => selectors.test(key))
            .reduce((prev, key) => (prev[key] = obj[key], prev), {});

        return mergeDeep(prev, subobj);;
    }, {});

    if(replace)
        res = replaceElement(res, replace);

    return res;
};

const mergeDeep = (...objects) => {
    const isObject = obj => obj && typeof obj === 'object';

    return objects.reduce((prev, obj) => {
        Object.keys(obj).forEach(key => {
            const pVal = prev[key];
            const oVal = obj[key];

            if(Array.isArray(pVal) && Array.isArray(oVal)) {
                prev[key] = pVal.concat(...oVal);
            } else if(isObject(pVal) && isObject(oVal)) {
                prev[key] = mergeDeep(pVal, oVal);
            } else {
                prev[key] = oVal;
            }
        });

        return prev;
    }, {});
};

const replaceElement = (styles, element) => {
    const regexp = new RegExp(`^${element}([^a-z0-9]|$)`);

    const res = {};
    for(let key of Object.keys(styles)) {
        const selector = key
            .split(',').map(part => part.trim())
            .map(part => part.replace(regexp, '& ').trim())
            .join(', ');

        res[selector] = styles[key];
    }

    return res;
};
