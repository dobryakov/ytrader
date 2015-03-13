<?php
/* @var $this SponsorsiteController */
/* @var $model Sponsorsite */

$this->breadcrumbs=array(
	'Sponsorsites'=>array('index'),
	'Import galleries',
);

$this->menu=array(
	array('label'=>'List Sponsorsite', 'url'=>array('index')),
	array('label'=>'Manage Sponsorsite', 'url'=>array('admin')),
);
?>

<h1>Import sponsorsite galleries</h1>

<p>
    <?=$result?>
</p>

<form action="" method="post">
    <div class="row">
        Sponsorsite id<br/>
        <!--input type="text" value="<?=$sponsorsite_id?>" name="sponsorsite_id" <?= $sponsorsite_id ? 'disabled="disabled"' : '' ?>/>
        <a href="<?=Yii::app()->createUrl('sponsorsite/update', array('id'=>$sponsorsite_id))?>"><?=$sponsorsite_name?></a-->
		<?

		// делаем выборку всех спонсоров из базы данных
		$models = Sponsorsite::model()->findAll(
			array('order' => 'name'));

		// при помощи listData создаем массив вида $ключ => $значение
		$list = CHtml::listData($models,
			'id', 'name');

		echo CHtml::dropDownList('sponsorsite_id', $sponsorsite_id,
			$list,
			array('empty' => 'Select sponsor site'));
		?>
    </div>
    <div class="row">
        Galleries<br/>
        <textarea name="sponsorsite_galleries" style="width:700px; height:300px;"></textarea>
    </div>
    <div class="row">
        Content type<br/>
        <!--select name="sponsorsite_contenttype">
            <option value="1" <?= $content_type==1 ? 'selected="selected"' : '' ?>>pics</option>
            <option value="2" <?= $content_type==2 ? 'selected="selected"' : '' ?>>movies</option>
            <option value="4" <?= $content_type==4 ? 'selected="selected"' : '' ?>>embed</option>
        </select-->
		<?
		// при помощи listData создаем массив вида $ключ=>$значение
		$list = ContenttypeController::getList();

		echo CHtml::dropDownList('sponsorsite_contenttype', $content_type,
			$list,
			array('empty' => 'Select content type'));
		?>
	</div>
	<div class="row">
		Tags<br/>
        <input type="text" value="" name="sponsorsite_tags"/>
    </div>
    <div class="row">
		<br/>
        <input type="submit"/>
    </div>
</form>

