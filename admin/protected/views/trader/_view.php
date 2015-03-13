<?php
/* @var $this TraderController */
/* @var $data Trader */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('host')); ?>:</b>
	<?php echo CHtml::encode($data->host); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('url')); ?>:</b>
	<?php echo CHtml::encode($data->url); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('section_id')); ?>:</b>
	<?php echo CHtml::encode($data->section_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('daily_in')); ?>:</b>
	<?php echo CHtml::encode($data->daily_in); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('daily_out')); ?>:</b>
	<?php echo CHtml::encode($data->daily_out); ?>
	<br />


</div>