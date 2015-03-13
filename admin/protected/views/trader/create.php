<?php
/* @var $this TraderController */
/* @var $model Trader */

$this->breadcrumbs=array(
	'Traders'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Trader', 'url'=>array('index')),
	array('label'=>'Manage Trader', 'url'=>array('admin')),
);
?>

<h1>Create Trader</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>