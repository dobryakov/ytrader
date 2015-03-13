<? /** @var $nextThumb Image */ ?>
<? /** @var $model Section */?>
<table class="face_table" cellpadding="0" cellspacing="0">
<?
	for($row=1;$row<=$rows;$row++) {
		?><tr><?
		for($col=1;$col<=$cols;$col++) {
			$nextThumb = $model->getNextThumb();
			if ($nextThumb) {
				?>
				<td>
					<?=$nextThumb->makeThumbHTML($cropprofile->id)?>
					<div class="section_thumb_subtext">
						Views: <?=intval($nextThumb->shows)?>
						<br/>
						<?=$nextThumb->shortenDesc(40)?>
					</div>
				</td>
				<?
			}
		}
		?></tr><?
	}
?>
</table>
