<?php
/* @var $this SponsorsiteController */
/* @var $model Sponsorsite */

$this->breadcrumbs=array(
	'Sponsorsites'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Sponsorsite', 'url'=>array('index')),
	array('label'=>'Manage Sponsorsite', 'url'=>array('admin')),
);
?>

<h1>Create Sponsorsite</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>