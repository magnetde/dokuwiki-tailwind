document.addEventListener('DOMContentLoaded', () => {
    // We only care about admin pages
    if (typeof JSINFO === 'undefined' || JSINFO.ACT !== 'admin') return;

    // Track default values and toggle .default class
    document.querySelectorAll('.dw-action-admin table.settings input, .dw-action-admin table.inline input, .dw-action-admin table.settings select, .dw-action-admin table.inline select').forEach(input => {
        const tr = input.closest('tr');
        const td = input.closest('td.value');
        if (!tr && !td) return;

        const isCurrentlyDefault = (tr && tr.classList.contains('default')) || (td && td.classList.contains('default'));
        
        let defaultValue;
        if (input.type === 'checkbox' || input.type === 'radio') {
            // If the row starts with .default, its current checked state is its default state.
            // If it does not start with .default, its default state must be the opposite of its current checked state.
            defaultValue = isCurrentlyDefault ? input.checked : !input.checked;
        } else {
            // For text/select, we can only easily infer the default if it starts as default.
            if (!isCurrentlyDefault) return; 
            defaultValue = input.value;
        }

        const handleChange = () => {
            let matchesDefault = false;
            if (input.type === 'checkbox' || input.type === 'radio') {
                matchesDefault = (input.checked === defaultValue);
            } else {
                matchesDefault = (input.value === defaultValue);
            }

            // Only modify elements that were originally default or are becoming default
            if (tr && (tr.classList.contains('default') || matchesDefault)) {
                tr.classList.toggle('default', matchesDefault);
            }
            if (td && (td.classList.contains('default') || matchesDefault)) {
                td.classList.toggle('default', matchesDefault);
            }
        };

        input.addEventListener('change', handleChange);
        if (input.type !== 'checkbox' && input.type !== 'radio') {
            input.addEventListener('input', handleChange);
        }
    });
});
