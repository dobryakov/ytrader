<?php
/* @var $this SponsorfeedController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Sponsorfeeds',
);

$this->menu=array(
	array('label'=>'Create Sponsorfeed', 'url'=>array('create')),
	array('label'=>'Manage Sponsorfeed', 'url'=>array('admin')),
);
?>

<h1>Sponsorfeeds</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
