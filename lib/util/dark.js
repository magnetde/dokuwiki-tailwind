module.exports = (css) => {
    return {
        '@media (prefers-color-scheme: dark)': {
            ...css
        }
    };
};
