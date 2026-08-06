try {
    if (/(Mac|iPhone|iPod|iPad)/i.test(navigator.platform)) {
        document.documentElement.classList.add('os-macos')
    }
} catch (_) {}

document.addEventListener('keydown', (event) => {
    const searchInput = document.getElementsByName('q')[0];

    const isCmdK = event.metaKey && event.key === 'k';
    const isCtrlK = event.ctrlKey && event.key === 'k';

    if (isCmdK || isCtrlK) {
        event.preventDefault();
        searchInput.focus();
    }
});

// Prevent search box from closing when opening a result in a new tab
document.addEventListener('DOMContentLoaded', () => {
    const qsearchIn = document.getElementById('qsearch__in');
    if (qsearchIn) {
        qsearchIn.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && (e.metaKey || e.ctrlKey)) {
                e.stopPropagation(); // Stop DokuWiki from handling the enter key
                e.preventDefault();
                const form = qsearchIn.closest('form');
                if (form) {
                    form.target = '_blank';
                    form.submit();
                    setTimeout(() => form.target = '_self', 100);
                }
            }
        }, { capture: true });
    }

    const qsearchOut = document.getElementById('qsearch__out');
    if (qsearchOut) {
        // Prevent input blur when clicking inside the results
        qsearchOut.addEventListener('mousedown', (event) => {
            if (event.metaKey || event.ctrlKey) {
                event.preventDefault();
            }
        });

        // DokuWiki attaches a click listener to #qsearch__out that clears the results.
        // We use a MutationObserver to attach click listeners directly to the dynamic links
        // so we can stop the event from bubbling up to #qsearch__out.
        const observer = new MutationObserver(() => {
            const links = qsearchOut.querySelectorAll('a');
            links.forEach(link => {
                if (!link.dataset.qsearchBound) {
                    link.dataset.qsearchBound = "true";
                    link.addEventListener('click', (e) => {
                        if (e.metaKey || e.ctrlKey) {
                            e.stopPropagation();
                            e.preventDefault();
                            window.open(link.href, '_blank');
                        }
                    });
                }
            });
        });

        observer.observe(qsearchOut, { childList: true, subtree: true });
    }
});
