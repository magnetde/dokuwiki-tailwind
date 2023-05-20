<?php
/**
 * Media Manager Popup
 */

// must be run from within DokuWiki
if(!defined('DOKU_INC'))
	die();

@require_once dirname(__FILE__) . '/inc/global.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="popup no-js">
<head>
	<?php require_once dirname(__FILE__) . '/inc/ui/meta.php'; ?>
	<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
</head>

<body>
	<div id="media__manager" class="mediamanager-popup">
		<?php html_msgarea() ?>

		<div class="mediamanager-main">
			<nav id="mediamgr__aside" class="mediamanager-aside">
				<div class="pad">
					<h1><?php echo hsc($lang['mediaselect'])?></h1>

					<?php /* keep the id! additional elements are inserted via JS here */?>
					<div id="media__opts" class="media-opts"></div>

					<?php _tpl_mediaTree() ?>
				</div>
			</nav>

			<main id="mediamgr__content" class="mediamanager-content">
				<div class="pad">
					<?php tpl_mediaContent() ?>
				</div>
			</main>
		</div>
	</div>
</body>
</html>
