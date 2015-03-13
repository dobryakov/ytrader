<h3 style="">
	&laquo;<a href="<?=$controller->createUrl("site/out", array('id' => $model->id))?>" onClick="_gaq.push(['_trackPageview','paysite']);" target="<?=TARGET?>"><?=$model->name?></a>&raquo;
</h3>

Our members rating:
<? for ($s=1;$s<=5;$s++) {
	$stars = round($model->stars) >= 1 ? round($model->stars) : rand(2,3);
	?>
	<img src="/images/<?=$this->controller->CURRENT_SITE->id?>/<?= ($s>$stars ? 'graystar' : 'star') ?>.png" border="0" alt="" class="paysite_star" align="absmiddle"/>
<? } ?>
<br/>


<? if ($model->join_cost) { ?>
Membership: $<?=$model->join_cost?>
<br/>
<? } ?>

<? if ($model->trial_cost) { ?>
Trial access: $<?=$model->trial_cost?>
<br/>
<? } ?>
<a class="paysite_join_link" href="<?=$controller->createUrl("site/out", array('id' => $model->id))?>" onClick="_gaq.push(['_trackPageview','paysite']);" target="<?=TARGET?>">See membership prices</a>
<br/>
<br/>
