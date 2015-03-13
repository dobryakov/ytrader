<?php
/* @var $this SponsorsiteController */
/* @var $model Sponsorsite */

$this->breadcrumbs=array(
	'Sponsorsites'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Sponsorsite', 'url'=>array('index')),
	array('label'=>'Create Sponsorsite', 'url'=>array('create')),
	array('label'=>'Update Sponsorsite', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Sponsorsite', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Sponsorsite', 'url'=>array('admin')),
);
?>

<h1>View Sponsorsite #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'sponsor_id',
		'name',
		'tags',
		'join_url',
		'outs',
		'content_rank',
	),
)); ?>

<br/>
<p>
	<a href="<?=Yii::app()->createUrl('sponsor/view', array('id' => $model->sponsor_id))?>">Спонсор</a>
	<br/>
    <a href="<?=Yii::app()->createUrl('sponsorsite/import', array('sponsorsite_id' => $model->id))?>">Импорт галерей</a>
</p>

<p>
Фиды:
<br/>
	<? foreach($model->feeds as $feed) { ?>
	<a href="<?=Yii::app()->createUrl('sponsorfeed/view', array('id'=>$feed->id));?>"><?=$feed->url?></a><br/>
	<? } ?>
</p>