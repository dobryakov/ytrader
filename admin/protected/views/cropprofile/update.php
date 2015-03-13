<?php
/* @var $this CropprofileController */
/* @var $model Cropprofile */

$this->breadcrumbs=array(
	'Cropprofiles'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Cropprofile', 'url'=>array('index')),
	array('label'=>'Create Cropprofile', 'url'=>array('create')),
	array('label'=>'View Cropprofile', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Cropprofile', 'url'=>array('admin')),
);
?>

<h1>Update Cropprofile <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>