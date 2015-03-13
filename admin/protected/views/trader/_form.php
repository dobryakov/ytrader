<?php
/* @var $this TraderController */
/* @var $model Trader */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'trader-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'host'); ?>
		<?php echo $form->textArea($model,'host',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'host'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'url'); ?>
		<?php echo $form->textArea($model,'url',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'url'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'section_id'); ?>
		<?php echo $form->textField($model,'section_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'section_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'daily_in'); ?>
		<?php echo $form->textField($model,'daily_in',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'daily_in'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'daily_out'); ?>
		<?php echo $form->textField($model,'daily_out',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'daily_out'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->