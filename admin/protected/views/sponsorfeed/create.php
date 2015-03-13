<?php
/* @var $this SponsorfeedController */
/* @var $model Sponsorfeed */

$this->breadcrumbs=array(
	'Sponsorfeeds'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Sponsorfeed', 'url'=>array('index')),
	array('label'=>'Manage Sponsorfeed', 'url'=>array('admin')),
);
?>

<h1>Create Sponsorfeed</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>