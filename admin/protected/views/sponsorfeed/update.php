<?php
/* @var $this SponsorfeedController */
/* @var $model Sponsorfeed */

$this->breadcrumbs=array(
	'Sponsorfeeds'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Sponsorfeed', 'url'=>array('index')),
	array('label'=>'Create Sponsorfeed', 'url'=>array('create')),
	array('label'=>'View Sponsorfeed', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Sponsorfeed', 'url'=>array('admin')),
);
?>

<h1>Update Sponsorfeed <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>