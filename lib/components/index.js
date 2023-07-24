const apply = require('../util/apply');

const link = (color, dark) => (
    apply`font-medium no-underline decoration-current hover:underline text-[${color}] ${dark ? `dark:text-[${dark}]` : ''}`
);

const dropdownContainer = apply`
    z-10 hidden divide-y rounded-md focus:outline-none
    bg-white divide-gray-100 ring-black ring-opacity-5 ring-1 shadow-lg
    dark:bg-gray-700 dark:divide-gray-600 dark:ring-0 dark:shadow-none
`;

const dropdownElement = apply`
    block px-4 py-2 w-full text-sm
    text-gray-700 hover:text-gray-900 hover:bg-gray-100
    dark:text-gray-200 dark:hover:text-white dark:hover:bg-gray-600
`;

module.exports = {
    link,
    dropdownContainer,
    dropdownElement,
}
