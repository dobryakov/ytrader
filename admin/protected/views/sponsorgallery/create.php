<?php
/* @var $this SponsorgalleryController */
/* @var $model Sponsorgallery */

$this->breadcrumbs=array(
	'Sponsorgalleries'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Sponsorgallery', 'url'=>array('index')),
	array('label'=>'Manage Sponsorgallery', 'url'=>array('admin')),
);
?>

<h1>Create Sponsorgallery</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>