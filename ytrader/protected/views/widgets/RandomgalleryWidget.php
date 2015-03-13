<?php

class RandomgalleryWidget extends CWidget
{

	public $rows;
	public $cols;

	public function init()
	{
		$controller = $this->owner;
		//$gallery = Gallery::model()->find("section_id=".$controller->image->gallery->section_id." ORDER BY RAND()");

		$this->render('randomgallery', array(
			/*"model" => $gallery,*/
			"rows" => $this->rows,
			"cols" => $this->cols,
			"controller" => $controller,
		));

		/*
		$gallery = $controller->gallery;
		$thumb = Image::model()->find("gallery_id=".$gallery->id." ORDER BY RAND()");
		$cropprofile = Cropprofile::model()->cache(DEFAULT_CACHE_TIME)->find("section_id=".$gallery->section_id." AND assignment & ".Cropprofile::ASSIGN_FACE);
		if ($thumb && $cropprofile) {

			// рисуем шаблон
			$this->render('randomthumb', array(
				"model" => $thumb,
				"cropprofile" => $cropprofile,
				"controller" => $controller,
			));

		}
		*/
	}

	public function run()
	{
	}
}

?>