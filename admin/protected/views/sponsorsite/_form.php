<?php
/* @var $this SponsorsiteController */
/* @var $model Sponsorsite */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sponsorsite-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<!--div class="row">
		<?php echo $form->labelEx($model,'sponsor_id'); ?>
		<?php echo $form->textField($model,'sponsor_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'sponsor_id'); ?>
	</div-->
	<?

	// делаем выборку всех спонсоров из базы данных
	$models = Sponsor::model()->findAll(
		array('order' => 'name'));

	// при помощи listData создаем массив вида $ключ=>$значение
	$list = CHtml::listData($models,
		'id', 'name');

	echo CHtml::dropDownList('Sponsorsite[sponsor_id]', $model->sponsor_id,
	$list,
	array('empty' => 'Select sponsor'));
	?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textArea($model,'name',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tags'); ?>
		<?php echo $form->textArea($model,'tags',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'tags'); ?>
		<p>Слова, характеризующие сайт, маленькими буквами через запятую</p>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'join_url'); ?>
		<?php echo $form->textArea($model,'join_url',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'join_url'); ?>
		<p>Ссылка для регистрации клиентов</p>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'join_cost'); ?>
        <?php echo $form->textField($model,'join_cost',array('size'=>20,'maxlength'=>20)); ?>
        <?php echo $form->error($model,'join_cost'); ?>
		<p>Стоимость полной подписки (обычно за месяц)</p>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'trial_cost'); ?>
        <?php echo $form->textField($model,'trial_cost',array('size'=>20,'maxlength'=>20)); ?>
        <?php echo $form->error($model,'trial_cost'); ?>
		<p>Стоимость пробной подписки (обычно за три дня)</p>
    </div>

	<!--div class="row">
		<?php echo $form->labelEx($model,'outs'); ?>
		<?php echo $form->textField($model,'outs',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'outs'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'content_rank'); ?>
		<?php echo $form->textField($model,'content_rank'); ?>
		<?php echo $form->error($model,'content_rank'); ?>
	</div-->

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->