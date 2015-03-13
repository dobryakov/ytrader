<?php
/* @var $this TraderController */
/* @var $model Trader */

$this->breadcrumbs=array(
	'Traders'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Trader', 'url'=>array('index')),
	array('label'=>'Create Trader', 'url'=>array('create')),
	array('label'=>'Update Trader', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Trader', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Trader', 'url'=>array('admin')),
);
?>

<h1>View Trader #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'host',
		'url',
		'section_id',
		'daily_in',
		'daily_out',
	),
)); ?>
