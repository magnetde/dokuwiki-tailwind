// Initialize toolbar at page load
initializeToolbar();

// Update the toolbar, if the dark mode changed
window.addEventListener('storage', (event) => {
	if(event.key === 'theme')
		updateToolbarIcons();
});

// Same with media queries
const mql = window.matchMedia('(prefers-color-scheme: dark)');
mql.addEventListener('change', (e) => {
	updateToolbarIcons();
});

// Initializes the toolbar.
function initializeToolbar() {
	if(typeof window.toolbar === 'undefined')
		return false;

	var icons = {
		'bold.png': 'format-bold',
		'chars.png': 'special-character',
		'h.png': 'format-header-pound',
		'h1.png': 'format-header-1',
		'hequal.png': 'format-header-equal',
		'hminus.png': 'format-header-decrease',
		'hplus.png': 'format-header-increase',
		'hr.png': 'minus', // ??
		'image.png': 'image',
		'italic.png': 'format-italic',
		'link.png': 'link',
		'linkextern.png': 'link-variant', // ??
		'mono.png': 'format-title',
		'ol.png': 'format-list-numbered',
		'sig.png': 'signature',
		'smiley.png': 'emoticon-outline',
		'strike.png': 'format-strikethrough',
		'ul.png': 'format-list-bulleted',
		'underline.png': 'format-underline',
	};

	function iconURL(name) {
		return `${DOKU_BASE}lib/tpl/tailwind/icon.php?icon=${name}&color=${encodeURIComponent(toolbarIconColor())}`;
	};

	for(var i in window.toolbar) {
		if(window.toolbar[i].icon == 'h.png') {
			for(var x in window.toolbar[i].list) {
				var hn = parseInt(x) + 1;
				window.toolbar[i].list[x].icon = iconURL('format-header-' + hn);
			}
		}

		var icon = window.toolbar[i].icon;

		if(icon in icons) {
			window.toolbar[i].icon = iconURL(icons[icon]);
		}
	}
}

function toolbarIconColor() {
	if(localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches))
		return 'gray-400';
	else
		return 'gray-500';
}

function updateToolbarIcons() {
	function updateQueryStringParameter(url, key, value) {
		var re = new RegExp('([?&])' + key + '=.*?(&|$)', 'i');
		var separator = url.indexOf('?') !== -1 ? '&' : '?';
		if(url.match(re))
			return url.replace(re, '$1' + key + '=' + encodeURIComponent(value) + '$2');
		else
			return url + separator + key + '=' + encodeURIComponent(value);
	}

	function updateColor(url) {
		if(!url || !url.includes('tailwind/icon.php'))
			return url;

		return updateQueryStringParameter(url, 'color', toolbarIconColor());
	}

	for(var i in window.toolbar) {
		if(window.toolbar[i].list) {
			for(var x in window.toolbar[i].list) {
				window.toolbar[i].list[x].icon = updateColor(window.toolbar[i].list[x].icon);
			}
		}

		window.toolbar[i].icon = updateColor(window.toolbar[i].icon);
	}

	// initToolbar is defined in 'toolbar.js' in the DokuWiki repo.
	initToolbar('tool__bar', 'wiki__text', window.toolbar);
};
