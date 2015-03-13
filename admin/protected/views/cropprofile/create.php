<?php
/* @var $this CropprofileController */
/* @var $model Cropprofile */

$this->breadcrumbs=array(
	'Cropprofiles'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Cropprofile', 'url'=>array('index')),
	array('label'=>'Manage Cropprofile', 'url'=>array('admin')),
);
?>

<h1>Create Cropprofile</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>