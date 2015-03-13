<?php
/* @var $this SponsorgalleryController */
/* @var $model Sponsorgallery */

$this->breadcrumbs=array(
	'Sponsorgalleries'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Sponsorgallery', 'url'=>array('index')),
	array('label'=>'Create Sponsorgallery', 'url'=>array('create')),
	array('label'=>'Update Sponsorgallery', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Sponsorgallery', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Sponsorgallery', 'url'=>array('admin')),
);
?>

<h1>View Sponsorgallery #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'site_id',
		'url',
		'tags',
		'name',
		'description',
		'content_type',
		'suffix',
		'gallery_id',
	),
)); ?>
