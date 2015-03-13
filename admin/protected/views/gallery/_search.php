<?php
/* @var $this GalleryController */
/* @var $model Gallery */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'section_id'); ?>
		<?php echo $form->textField($model,'section_id',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'sponsorgallery_id'); ?>
		<?php echo $form->textField($model,'sponsorgallery_id',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'crop_status'); ?>
		<?php echo $form->textField($model,'crop_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'show_status'); ?>
		<?php echo $form->textField($model,'show_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'t_create'); ?>
		<?php echo $form->textField($model,'t_create',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'rank'); ?>
		<?php echo $form->textField($model,'rank'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->