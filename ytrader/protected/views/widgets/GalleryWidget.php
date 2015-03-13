<?php

/**
 * Виджет рекомендуемых галерей, подбираемых по "ярлычкам" пользователя,
 * которые он "насобирал", путешествуя по сайтам
 */
class GalleryWidget extends CWidget
{

	public $model;
	public $rows;
	public $cols;

    public function init()
    {

		$controller = $this->owner;
		$cropprofile = Cropprofile::model()->find("section_id=".$this->model->section_id." AND assignment & ".Cropprofile::ASSIGN_GALLERY);

		$this->render('gallery', array(
			"model" => $this->model,
			"controller" => $controller,
			"cropprofile_id" => $cropprofile->id,
			"rows" => $this->rows,
			"cols" => $this->cols,
		));

	}
 
    public function run()
    {
	}
}

?>