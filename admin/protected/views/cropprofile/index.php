<?php
/* @var $this CropprofileController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Cropprofiles',
);

$this->menu=array(
	array('label'=>'Create Cropprofile', 'url'=>array('create')),
	array('label'=>'Manage Cropprofile', 'url'=>array('admin')),
);
?>

<h1>Cropprofiles</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
