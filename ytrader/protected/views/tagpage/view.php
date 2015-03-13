<?php
/* @var $this TagpageController */
/* @var $sponsorgalleries Sponsorgallery */
/* @var $gallery Gallery */

$this->breadcrumbs=array(
	'Sections'=>array('index'),
	$tag,
);

?>

<?php
if ($sponsorgalleries) {

	foreach ($sponsorgalleries as $sponsorgallery) {

		if ($sponsorgallery->gallery &&
			$sponsorgallery->gallery->crop_status == Gallery::STATUS_CROPPED) {

			$gallery = $sponsorgallery->gallery;

			?>
			<?=$gallery->getNextThumb()?>
			<?

		}

	}

}
?>