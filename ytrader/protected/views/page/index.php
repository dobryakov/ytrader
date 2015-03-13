<?php
/* @var $this PageController */

$this->breadcrumbs=array(
	$model->gallery->section->name . ' ' . ucfirst(ContenttypeController::getString($model->gallery->content_type)) . ' View' => array('section/view', 'id'=>$model->gallery->section->name),
	'Gallery '.$model->gallery->name => array('gallery/index', 'id'=>$this->gallery->id),
	$this->pageTitle,
);

$cropprofile = Cropprofile::model()->cache(DEFAULT_CACHE_TIME)->find("section_id=".$this->gallery->section_id." AND assignment & ".Cropprofile::ASSIGN_PAGE);
if ($cropprofile) {
	$cropprofile_id = $cropprofile->id;
	$url_path = $this->image->getThumbpath($cropprofile_id); //$url_path = '/thumbs/'.$this->gallery->id.'/'.$cropprofile_id.'/'.$this->image->id.'.jpg';
}

?>

<? if(isset($this->breadcrumbs)) { ?>
<div id="breadcrumbs">
<? $this->widget('zii.widgets.CBreadcrumbs', array(
		'homeLink'=>'<a href="/">'.$this->CURRENT_SITE->name.'</a>',
		'links'=>$this->breadcrumbs,
	)); ?>
</div>
<? } ?>

<br/>

<table border="0" align="left" cellpadding="0" cellspacing="0" id="image_page_table">
	<tr valign="top">
		<td>

			<table border="0" id="image_table" cellpadding="0" cellspacing="0">

				<tr valign="top">
				<td>

				<? if ($model->gallery->content_type == ContenttypeController::CONTENT_TYPE_IMAGES) { ?>
				<a href="<?=$this->createUrl("site/out", array("id"=>$model->gallery->sponsorgallery->site->id))?>" onClick="_gaq.push(['_trackPageview','paysite']);" target="<?=TARGET?>" rel="nofollow">
					<img src="<?=$url_path?>" class="main_image_img" style="width: <?=$cropprofile->width?>px; height: <?=$cropprofile->height?>px;"/>
				</a>
				<? } ?>

				<? if ($model->gallery->content_type == ContenttypeController::CONTENT_TYPE_EMBEDS) { ?>
				<div class="flash_player" style="width: <?=$cropprofile->width?>px; margin:0px 15px 15px 0px;">
					<div id="china_1" align="center" style="vertical-align:middle;border:1px #666666 solid;"></div>
					<script type="text/javascript">
						var so1 = new SWFObject('<?=Controller::getStaticBase()?>/flvs/flvplayer.swf','mpl','<?=$cropprofile->width?>','<?=$cropprofile->height?>','7');
						so1.addParam('allowfullscreen','false');
						so1.addParam('allowscriptaccess','always');
						so1.addVariable('usefullscreen','false');
						so1.addVariable('file','<?=$model->getFlvpath($cropprofile->id)?>');
						so1.addVariable('image','<?=$url_path?>');
						so1.addVariable('width','<?=$cropprofile->width?>');
						so1.addVariable('height','<?=$cropprofile->height?>');
						so1.addVariable('displayheight','<?=$cropprofile->height?>');
						so1.addVariable('frontcolor','0x322014');
						so1.addVariable('backcolor','0xFBDC05');
						so1.write('china_1');
					</script>
				</div>
				<? } ?>

				</td>
				<td>

				<div>
					<?php $this->widget('application.views.widgets.RandomthumbWidget', array(
					"rows" => 1,
					"cols" => 2,
					"exclude" => array($model->id),
				)); ?>
				</div>

				<div>
					<?php $this->widget('application.views.widgets.PaysiteWidget'); ?>
				</div>

				</td>
				</tr>

			</table>

		</td>
		<td id="page_suggest_divider"></td>
		<td>

			<div class="suggest_div">
				<?php $this->widget('application.views.widgets.SuggestlinksWidget',
					array(
						'rows' => $this->CURRENT_SITE->suggest_rows ? $this->CURRENT_SITE->suggest_rows : 2,
						'cols' => $this->CURRENT_SITE->suggest_cols ? $this->CURRENT_SITE->suggest_cols : 2,
						'content_type' => $model->gallery->content_type,
					)
				); ?>
			</div>

		</td>
	</tr>
</table>

<div style="clear:both;">
</div>

<h4>Random galleries:</h4>
<?php $this->widget('application.views.widgets.RandomgalleryWidget',
	array(
		'rows' => $this->CURRENT_SITE->random_rows ? $this->CURRENT_SITE->random_rows : 1,
		'cols' => $this->CURRENT_SITE->random_cols ? $this->CURRENT_SITE->random_cols : 6,
	)
); ?>

<? if (Controller::getRemoteAddr()=='93.157.124.13') { ?>
<p>
	[<a href="http://ytrader-admin...com/index.php?r=gallery/view&id=<?=$model->gallery->id?>">DELETE CONTENT</a>]
</p>
<? } ?>