<?php
/* @var $this GalleryController */
/* @var $data Gallery */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('section_id')); ?>:</b>
	<?php echo CHtml::encode($data->section_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sponsorgallery_id')); ?>:</b>
	<?php echo CHtml::encode($data->sponsorgallery_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('crop_status')); ?>:</b>
	<?php echo CHtml::encode($data->crop_status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('show_status')); ?>:</b>
	<?php echo CHtml::encode($data->show_status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('t_create')); ?>:</b>
	<?php echo CHtml::encode($data->t_create); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('rank')); ?>:</b>
	<?php echo CHtml::encode($data->rank); ?>
	<br />


</div>