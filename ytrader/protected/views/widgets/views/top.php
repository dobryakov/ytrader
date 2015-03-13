<table class="traderstop_table" cellpadding="0" cellspacing="0">
<?
	for($row=1;$row<=$rows;$row++) {
		?><tr><?
		for($col=1;$col<=$cols;$col++) {
			$trader = $trader_model->getNextTrader($model->id);
			$i = $col+($cols*($row-1));
			?>
				<td>
					<?=$i?>.
					<? if ($trader) { ?>
					<a target="<?=TARGET?>" href="<?= Yii::app()->createUrl('trader/out', array('id' => $trader->id)); ?>">
						<?= $trader->host ?>
					</a>
					<? } ?>
				</td>
			<?
		}
		?></tr><?
	}
?>
</table>
