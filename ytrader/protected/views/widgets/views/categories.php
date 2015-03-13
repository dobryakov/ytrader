<? if ($show_header) { ?>
<div class="categories_header">
    <?=$header?>
</div>
<? } ?>

<table class="<?=$table_css_class?>" cellpadding="0" cellspacing="0">
	<?
	for ($row=1;$row<=$rows;$row++) {
		?><tr>
			<?
			for ($col=1;$col<=$cols;$col++) {
				$section = $this->getNext();
				if ($section) {
					?>
					<td>
						<a href="<?=$controller->createUrl("section/view", array("id"=>$section->name))?>"><?=$section->name?></a>
					</td>
					<?
				}
			}
			?></tr><?
	}
	?>
</table>

<div id="richcategories_divider">

</div>
