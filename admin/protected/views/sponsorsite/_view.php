<?php
/* @var $this SponsorsiteController */
/* @var $data Sponsorsite */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sponsor_id')); ?>:</b>
	<?php echo CHtml::encode($data->sponsor_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tags')); ?>:</b>
	<?php echo CHtml::encode($data->tags); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('join_url')); ?>:</b>
	<?php echo CHtml::encode($data->join_url); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('outs')); ?>:</b>
	<?php echo CHtml::encode($data->outs); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('content_rank')); ?>:</b>
	<?php echo CHtml::encode($data->content_rank); ?>
	<br />


</div>