<? /** @var $model Gallery */ ?>
<table class="gallery_table" cellpadding="0" cellspacing="0">
<?php

for ($row=1;$row<=$rows;$row++) {
	?><tr><?
	for ($col=1;$col<=$cols;$col++) {

	?>
		<td>
			<?=$model->getNextThumb($cropprofile_id)?>
		</td>
	<?
	}
	?></tr><?
}

?>
</table>
