<?php
/* @var $this CropprofileController */
/* @var $model Cropprofile */

$this->breadcrumbs=array(
	'Cropprofiles'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Cropprofile', 'url'=>array('index')),
	array('label'=>'Create Cropprofile', 'url'=>array('create')),
	array('label'=>'Update Cropprofile', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Cropprofile', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Cropprofile', 'url'=>array('admin')),
);
?>

<h1>View Cropprofile #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'section_id',
		'width',
		'height',
		'assignment',
	),
)); ?>
