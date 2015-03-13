<?php
/* @var $this SponsorgalleryController */
/* @var $model Sponsorgallery */

$this->breadcrumbs=array(
	'Sponsorgalleries'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Sponsorgallery', 'url'=>array('index')),
	array('label'=>'Create Sponsorgallery', 'url'=>array('create')),
	array('label'=>'View Sponsorgallery', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Sponsorgallery', 'url'=>array('admin')),
);
?>

<h1>Update Sponsorgallery <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>