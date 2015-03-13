<?php
/* @var $this GalleryController */

$this->breadcrumbs=array(
    $this->gallery->section->name . ' ' . ucfirst(ContenttypeController::getString($this->gallery->section->content_type)) . ' View' => Yii::app()->createUrl('section/view', array('id' => $this->gallery->section->name)),
	' Gallery ' . $this->gallery->sponsorgallery->name,
);

?>

<? if(isset($this->breadcrumbs)) { ?>
<? $this->widget('zii.widgets.CBreadcrumbs', array(
		'homeLink'=>'<a href="/">'.$this->CURRENT_SITE->name.'</a>',
        'links'=>$this->breadcrumbs,
    )); ?>
<? } ?>

<br/>
<table border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td>
			<?php $this->widget('application.views.widgets.GalleryWidget',
			array(
				'rows' => $controller->CURRENT_SITE->gallery_rows ? $controller->CURRENT_SITE->gallery_rows : 5,
				'cols' => $controller->CURRENT_SITE->gallery_cols ? $controller->CURRENT_SITE->gallery_cols : 5,
				'model' => $model,
			)
			); ?>
			<p>
				<a class="paysite_join_link" href="<?= Yii::app()->createUrl('site/out', array('id'=>$model->sponsorgallery->site->id)) ?>" onClick="_gaq.push(['_trackPageview','paysite']);" target="<?=TARGET?>">Get instant access</a>
				or see more
				<a class="paysite_join_link" href="<?= Yii::app()->createUrl('section/view', array('id' => $model->section->name)) ?>"><?=$model->section->name?></a>
			</p>
		</td>
		<td id="gallery_suggest_divider"></td>
		<td id="gallery_suggest_column">
            <div class="suggest_div">
				<?php $this->widget('application.views.widgets.SuggestlinksWidget',
					array(
						'rows' => $this->CURRENT_SITE->suggest_rows ? $this->CURRENT_SITE->suggest_rows : 2,
						'cols' => $this->CURRENT_SITE->suggest_cols ? $this->CURRENT_SITE->suggest_cols : 2,
						'content_type' => $model->content_type,
					)
				); ?>
            </div>
            <p class="gallery_description">
				<?=$this->gallery->description?>
			</p>
			<p>
                <a class="paysite_join_link" href="<?= Yii::app()->createUrl('site/out', array('id'=>$model->sponsorgallery->site->id)) ?>" onClick="_gaq.push(['_trackPageview','paysite']);" target="<?=TARGET?>">See membership prices</a>
            </p>
		</td>
	</tr>
</table>



<? /*$this->endCache(); }*/ ?>

