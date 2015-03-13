<?php if($this->beginCache('page_'.$model->id.'_paysite', array('duration' => DEFAULT_CACHE_TIME))) { ?>
<h3 style="">
	Find more <?=$section_name?> <?=$content_type_name?> at website &laquo;<a href="<?=$controller->createUrl("site/out", array('id' => $model->id))?>" onClick="_gaq.push(['_trackPageview','paysite']);" target="<?=TARGET?>"><?=$model->name?></a>&raquo;
</h3>

Our members rating:
<? for ($s=1;$s<=5;$s++) { ?>
	<img src="/images/<?=$this->controller->CURRENT_SITE->id?>/<?= ($s>round($model->stars) ? 'graystar' : 'star') ?>.png" border="0" alt="" class="paysite_star" align="absmiddle"/>
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
<?php $this->endCache(); } ?>
