<?php
/* @var $this SectionController */
/* @var $model Section */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'section-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?
	// делаем выборку данных из activerecord
	$models = Site::model()->findAll(
		array('order' => 'name'));

	// при помощи listData создаем массив вида $ключ=>$значение
	$list = CHtml::listData($models,
		'id', 'name');

	echo CHtml::dropDownList('Section[site_id]', $model->site_id,
		$list,
		array('empty' => 'Select our site'));
	?>

	<!--div class="row">
		<?php echo $form->labelEx($model,'site_id'); ?>
		<?php echo $form->textField($model,'site_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'site_id'); ?>
	</div-->

	<?
	// при помощи listData создаем массив вида $ключ=>$значение
	$list = ContenttypeController::getList();

	echo CHtml::dropDownList('Section[content_type]', $model->content_type,
		$list,
		array('empty' => 'Select content type'));
	?>

	<!--div class="row">
		<?php echo $form->labelEx($model,'content_type'); ?>
		<?php echo $form->textField($model,'content_type'); ?>
		<?php echo $form->error($model,'content_type'); ?>
	</div-->

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textArea($model,'name',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tags'); ?>
		<?php echo $form->textArea($model,'tags',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'tags'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->