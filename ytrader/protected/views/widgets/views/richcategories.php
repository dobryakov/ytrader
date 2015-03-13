<? if ($show_header) { ?>
<div class="richcategories_header">
    <?=$header?>
</div>
<? } ?>

<table class="richcategories_table" cellpadding="0" cellspacing="0">
	<?
	for ($row=1;$row<=$rows;$row++) {
		?><tr>
			<?
			for ($col=1;$col<=$cols;$col++) {
				/** @var $section Section */
				$section = $this->getNext();
				if ($section) {
					$cropprofile = Cropprofile::model()->find("section_id=".$section->id." AND assignment & ".Cropprofile::ASSIGN_FACE);
					$nextThumb = $section->getNextThumb();
					if ($nextThumb) {
						?>
						<td>
							<?=$nextThumb->makeThumbHTML($cropprofile->id)?>
							<br/>
							<a class="richcategories_underthumb_link" href="<?=$controller->createUrl("section/view", array("id"=>$section->name))?>">
								<?=$section->name?>
							</a>
						</td>
						<?
					}
				}
			}
			?></tr><?
	}
	?>
</table>

<div id="richcategories_divider">

</div>