<?php if($this->beginCache('page_'.$model->id.'_randomthumbs', array('duration' => DEFAULT_CACHE_TIME))) { ?>
<table class="randomthumbs_table" cellpadding="0" cellspacing="0">
<?
	for ($row=1;$row<=$rows;$row++) {
		?><tr><?
		for ($col=1;$col<=$cols;$col++) {
			if (!$exclude) { $exclude = array(); }
				$thumb = Image::model()->find("gallery_id=".$model->id." AND id NOT IN(".join(',',$exclude).") ORDER BY RAND()");
				if ($thumb) {
					?>
					<td>
						<a class="random_thumb_link" href="<?=$thumb->pagepath?>">
							<img class="random_thumb" src="<?=$thumb->getThumbpath($cropprofile->id)?>" border="0" alt=""/>
						</a>
					</td>
					<?
					}
			}
		?></tr><?
	}
?>
</table>
<?php $this->endCache(); } ?>