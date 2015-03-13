<?php
/* @var $this SponsorsiteController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Sponsorsites',
);

$this->menu=array(
	array('label'=>'Create Sponsorsite', 'url'=>array('create')),
	array('label'=>'Manage Sponsorsite', 'url'=>array('admin')),
);
?>

<h1>Sponsorsites</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
