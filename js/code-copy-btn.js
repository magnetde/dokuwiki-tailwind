/**
 * Script to handle the DokuWiki core code-copy-btn styling and icons.
 * Adds classes based on the inner text (success, error, etc.) so we can style the SVG background.
 */
document.addEventListener('DOMContentLoaded', () => {
    if (typeof LANG === 'undefined') return;

    let tooltipIdCounter = 0;

    // We observe the body for newly added pre blocks with the copy button
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'childList') {
                document.querySelectorAll('button.code-copy-btn:not(.is-observed)').forEach(btn => {
                    btn.classList.add('is-observed');

                    // Check if parent is a pre block
                    const parent = btn.parentElement;
                    if (parent && parent.tagName === 'PRE') {
                        // Create wrapper so the button stays fixed on screen while pre scrolls horizontally
                        if (!parent.parentElement.classList.contains('code-wrapper')) {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'code-wrapper relative group';
                            parent.parentNode.insertBefore(wrapper, parent);
                            wrapper.appendChild(parent);
                            wrapper.appendChild(btn); // Move button outside pre
                        } else {
                            parent.parentElement.appendChild(btn);
                        }
                    }

                    // DokuWiki's core copy JS breaks because the button is no longer inside the <pre>.
                    // We must handle the copy manually and stop the event from reaching DokuWiki's document listener.
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        e.preventDefault();
                        
                        const pre = btn.parentElement.querySelector('pre');
                        if (!pre) return;
                        
                        const textToCopy = pre.innerText;
                        const originalText = btn.textContent;
                        
                        navigator.clipboard.writeText(textToCopy).then(() => {
                            btn.textContent = (typeof LANG !== 'undefined' && LANG["clipboard_success"]) ? LANG["clipboard_success"] : "Copied!";
                        }).catch(() => {
                            btn.textContent = (typeof LANG !== 'undefined' && LANG["clipboard_error"]) ? LANG["clipboard_error"] : "Error";
                        }).finally(() => {
                            setTimeout(() => {
                                btn.textContent = originalText;
                            }, 2000);
                        });
                    });

                    tooltipIdCounter++;
                    const tooltipId = 'tooltip-code-copy-' + tooltipIdCounter;
                    btn.setAttribute('data-tooltip-target', tooltipId);
                    btn.setAttribute('data-tooltip-placement', 'bottom');

                    const tooltipText = btn.textContent.trim();

                    // Create tooltip element
                    const tooltip = document.createElement('div');
                    tooltip.id = tooltipId;
                    tooltip.setAttribute('role', 'tooltip');
                    tooltip.className = 'tooltip-container opacity-0 invisible';

                    const textNode = document.createTextNode(tooltipText);
                    tooltip.appendChild(textNode);

                    const arrow = document.createElement('div');
                    arrow.className = 'tooltip-arrow';
                    arrow.setAttribute('data-popper-arrow', '');
                    tooltip.appendChild(arrow);

                    // Insert into document.body to avoid inheriting <pre> monospace font
                    document.body.appendChild(tooltip);

                    // Re-initialize flowbite tooltips if the function is globally available
                    if (typeof initTooltips === 'function') {
                        initTooltips();
                    }

                    const btnObserver = new MutationObserver(() => {
                        const text = btn.textContent.trim();

                        if (text === LANG["clipboard_success"]) {
                            btn.classList.remove('is-error');
                            btn.classList.add('is-success');
                        } else if (text === LANG["clipboard_error"]) {
                            btn.classList.remove('is-success');
                            btn.classList.add('is-error');
                        } else {
                            btn.classList.remove('is-success', 'is-error');
                        }
                    });

                    btnObserver.observe(btn, { childList: true, characterData: true, subtree: true });
                });
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});
