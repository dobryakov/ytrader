<?php
/* @var $this GalleryController */
/* @var $model Gallery */

$this->breadcrumbs=array(
	'Galleries'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Gallery', 'url'=>array('index')),
	array('label'=>'Create Gallery', 'url'=>array('create')),
	array('label'=>'Update Gallery', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Recrop Gallery', 'url'=>array('recrop', 'id'=>$model->id)),
	array('label'=>'Delete Gallery', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Gallery', 'url'=>array('admin')),
);
?>

<h1>View Gallery #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'section_id',
		'sponsorgallery_id',
		'crop_status',
		'show_status',
		't_create',
		'rank',
	),
)); ?>

<br/>
<p>
	<!--<a href="<?=Yii::app()->createUrl("sponsorgallery/index", array("id"=>$model->sponsorgallery_id))?>">Спонсорская галерея</a>-->
	Спонсорская галерея:<br/>
	<a target="_blank" href="<?=$model->sponsorgallery->url?>"><?=$model->sponsorgallery->url?></a>
	(<a href="<?=Yii::app()->createUrl("sponsorgallery/update", array("id"=>$model->sponsorgallery_id))?>">редактировать</a>)
</p>
<p>
	<a href="<?=Yii::app()->createUrl("sponsorsite/view", array("id"=>$model->sponsorgallery->site_id))?>">Спонсорский сайт</a>
	<br/>
	<a href="<?=Yii::app()->createUrl("site/view", array("id"=>$model->section->site_id))?>">Наш сайт</a>
	<br/>
	<a href="<?=Yii::app()->createUrl("section/view", array("id"=>$model->section_id))?>">Секция</a>
</p>