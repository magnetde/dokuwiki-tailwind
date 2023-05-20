<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
<link rel="apple-touch-icon" sizes="64x64" href="<?php echo tpl_basedir(); ?>images/apple-touch-icon.png">
<link rel="icon" type="image/x-icon" href="<?php echo tpl_basedir(); ?>images/favicon.ico">
<title>
	<?php tpl_pagetitle() ?> - <?php echo strip_tags($conf['title']) ?>
</title>
<?php
_tpl_metaheaders();

echo tpl_favicon(array(
	'favicon',
	'mobile',
))
?>

<?php tpl_includeFile('meta.html') ?>

<!-- CSS file -->
<!-- It is not possible to provide with "style.ini", because the CSS syntax is not compatible with the CSS parser used by DokuWiki. -->
<!-- See: https://github.com/tailwindlabs/tailwindcss/issues/7121 -->
<link href="<?php echo tpl_basedir(); ?>assets/css/main.css" rel="stylesheet" />

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

<!-- JS -->
<!-- Do not provide it with "script.js". -->
<script src="<?php echo tpl_basedir(); ?>assets/js/flowbite.min.js" type="text/javascript"></script>
<script src="<?php echo tpl_basedir(); ?>assets/js/bootstrap-scrollspy.min.js" type="text/javascript"></script>
