<?php
/* @var $this SponsorgalleryController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Sponsorgalleries',
);

$this->menu=array(
	array('label'=>'Create Sponsorgallery', 'url'=>array('create')),
	array('label'=>'Manage Sponsorgallery', 'url'=>array('admin')),
);
?>

<h1>Sponsorgalleries</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
