<div class="page group">
	<!-- detail start -->
	<?php
	if($ERROR):
		echo '<h1>'.$ERROR.'</h1>';
	else: ?>
		<?php if($REV) echo p_locale_xhtml('showrev');?>

		<div class="media-details">
			<div class="header">
				<span class="name">
					<?php echo nl2br(hsc(tpl_img_getTag('simple.title'))); ?>
				</span>
			</div>

			<div class="image">
				<?php tpl_img(); ?>
			</div>

			<div class="metadata">
				<?php tpl_img_meta(); ?>

				<dl>
					<?php
					echo '<dt>'.$lang['reference'].':</dt>';
					$media_usage = ft_mediause($IMG, true);

					if(count($media_usage) > 0) {
						foreach($media_usage as $path)
							echo '<dd>'.html_wikilink($path).'</dd>';
					}
					else
						echo '<dd>'.$lang['nothingfound'].'</dd>';
					?>
				</dl>

				<p><?php echo $lang['media_acl_warning']; ?></p>
			</div>
		</div>
	<?php endif; ?>
</div>
