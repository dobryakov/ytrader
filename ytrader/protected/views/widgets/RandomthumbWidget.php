<?php

class RandomthumbWidget extends CWidget
{
	public $rows;
	public $cols;
	public $exclude = array();

	public function init()
	{
		$controller = $this->owner;
		$gallery = $controller->gallery;
		//$thumb = Image::model()->find("gallery_id=".$gallery->id." ORDER BY RAND()");
		$cropprofile = Cropprofile::model()->cache(DEFAULT_CACHE_TIME)->find("section_id=".$gallery->section_id." AND assignment & ".Cropprofile::ASSIGN_FACE);
		if ($cropprofile) {

			// рисуем шаблон
			$this->render('randomthumb', array(
				"model" => $gallery,
				"cropprofile" => $cropprofile,
				"controller" => $controller,
				"rows" => $this->rows,
				"cols" => $this->cols,
				"exclude" => $this->exclude,
			));

		}
	}

	public function run()
	{
	}
}

?>