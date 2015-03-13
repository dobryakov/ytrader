<? /** @var $gallery Gallery */ ?>
<? /** @var $controller SuggestlinksWidget */ ?>
<!--h4 style="margin-top: 0px; padding-top: 0px;">Suggest galleries:</h4-->
<? if ($galleries) { ?>

<table class="suggest_table" cellpadding="0" cellspacing="0">
<?php

for ($row=1;$row<=$rows;$row++) {
	?><tr valign="top"><?
	for ($col=1;$col<=$cols;$col++) {

		if (!isset($gallery)) {
			$gallery = reset($galleries);
		} else {
			$gallery = next($galleries);
		}

		$descr = trim(htmlspecialchars($gallery->name));
		if (!$descr) {
			$descr = $gallery->section->name.' '.ContenttypeController::getString($gallery->section->content_type);
		}

		if ($gallery) {
			$cropprofile = Cropprofile::model()->find("section_id=".$gallery->section_id." AND assignment & ".Cropprofile::ASSIGN_GALLERY);
			?>
			<td>
				<?=$gallery->getNextthumb($cropprofile->id)?>
				<p class="suggest_description">
					<?=$descr?>
				</p>
			</td>
			<?
		}
	}
	?></tr><?
}

?>
</table>
<? } ?>