<?php
/* @var $this TraderController */
/* @var $model Trader */

$this->breadcrumbs=array(
	'Traders'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Trader', 'url'=>array('index')),
	array('label'=>'Create Trader', 'url'=>array('create')),
	array('label'=>'View Trader', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Trader', 'url'=>array('admin')),
);
?>

<h1>Update Trader <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>