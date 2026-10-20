jQuery(window).on('load', function() {
    if (LANG.plugins.aichat) {
        const hostElement = document.querySelectorAll('aichat-button');
        Array.from(hostElement).forEach((element,index) =>
        {
            let shadowRoot = element.shadowRoot; // Assuming an open shadow root is attached
            // const targetDialogElement = shadowRoot.querySelector('dialog');
            let targetOpenElement = shadowRoot.querySelector('button');
            let targetCloseElement = shadowRoot.querySelector('dialog > div > header > button.toggle');

            targetOpenElement.addEventListener('click', function() {
                jQuery('header').css('z-index', 'auto');
                jQuery('div:has(>#dw__toc)').css('z-index', -1);
            });

            targetCloseElement.addEventListener('click', function() {
                jQuery('header').css('z-index', 50);
                jQuery('div:has(>#dw__toc)').css('z-index', 'auto');
            });
        });
    }
});