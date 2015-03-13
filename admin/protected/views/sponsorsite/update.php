<?php
/* @var $this SponsorsiteController */
/* @var $model Sponsorsite */

$this->breadcrumbs=array(
	'Sponsorsites'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Sponsorsite', 'url'=>array('index')),
	array('label'=>'Create Sponsorsite', 'url'=>array('create')),
	array('label'=>'View Sponsorsite', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Sponsorsite', 'url'=>array('admin')),
);
?>

<h1>Update Sponsorsite <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>