<? /** @var $gallery Gallery */?>
<?php if($this->beginCache('page_'.$controller->image->id.'_randomgalleries', array('duration' => DEFAULT_CACHE_TIME))) { ?>
<table class="random_galleries" cellpadding="0" cellspacing="0">
<? for ($row=1;$row<=$rows;$row++) {
	?><tr><?
	for ($col=1;$col<=$cols;$col++) {
		$gallery = Gallery::model()->find("section_id=".$controller->image->gallery->section_id." ORDER BY RAND()");
		if ($gallery) {
			?>
			<td>
				<?=$gallery->getBestThumb()?>
			</td>
			<?
		}
	}
	?></tr><?
}
?>
</table>
<?php $this->endCache(); } ?>
