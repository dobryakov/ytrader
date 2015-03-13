<?php
/* @var $this SponsorfeedController */
/* @var $model Sponsorfeed */

$this->breadcrumbs=array(
	'Sponsorfeeds'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Sponsorfeed', 'url'=>array('index')),
	array('label'=>'Create Sponsorfeed', 'url'=>array('create')),
	array('label'=>'Update Sponsorfeed', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Sponsorfeed', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Sponsorfeed', 'url'=>array('admin')),
);
?>

<h1>View Sponsorfeed #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'url',
		'site_id',
		'content_type',
	),
)); ?>

<br/>
<p>
	<a href="<?=Yii::app()->createUrl('sponsorsite/view', array('id' => $model->site_id))?>">Платный сайт</a>
</p>