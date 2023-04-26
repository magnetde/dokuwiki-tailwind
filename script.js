(() => {
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

	for(var i in window.toolbar) {
		// Replace all icons in "H(eaders)" picker
		if(window.toolbar[i].icon == 'h.png') {
			for(var x in window.toolbar[i].list) {
				var hn = parseInt(x) + 1;
				window.toolbar[i].list[x].icon = '../../tpl/tailwind/icon.php?icon=format-header-' + hn;
			}
		}

		var icon = window.toolbar[i].icon;

		if(icon in icons) {
			window.toolbar[i].icon = '../../tpl/tailwind/icon.php?icon=' + icons[icon];
		}
	}
})();
