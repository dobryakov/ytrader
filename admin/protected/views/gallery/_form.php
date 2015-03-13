<?php
/* @var $this GalleryController */
/* @var $model Gallery */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'gallery-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'section_id'); ?>
		<?php echo $form->textField($model,'section_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'section_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sponsorgallery_id'); ?>
		<?php echo $form->textField($model,'sponsorgallery_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'sponsorgallery_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'crop_status'); ?>
		<?php echo $form->textField($model,'crop_status'); ?>
		<?php echo $form->error($model,'crop_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'show_status'); ?>
		<?php echo $form->textField($model,'show_status'); ?>
		<?php echo $form->error($model,'show_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'t_create'); ?>
		<?php echo $form->textField($model,'t_create',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'t_create'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'rank'); ?>
		<?php echo $form->textField($model,'rank'); ?>
		<?php echo $form->error($model,'rank'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->