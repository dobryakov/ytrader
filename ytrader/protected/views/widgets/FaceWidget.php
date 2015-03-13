<?php

class FaceWidget extends CWidget
{

	public $model;
	public $rows;
	public $cols;

	public function init()
    {
		$controller = $this->owner;
		$cropprofile = Cropprofile::model()->find("section_id=".$this->model->id." AND assignment & ".Cropprofile::ASSIGN_FACE);
		if ($cropprofile) {
			// рисуем шаблон
			$this->render('face', array(
				"cropprofile" => $cropprofile,
				"controller" => $controller,
				"rows" => $this->rows,
				"cols" => $this->cols,
				"model" => $this->model,
			));
		}
    }
 
    public function run()
    {
	}
}

?>