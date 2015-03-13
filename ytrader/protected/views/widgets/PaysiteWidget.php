<?php

class PaysiteWidget extends CWidget
{
    public function init()
    {
		$controller = $this->owner;
		$paysite = $controller->image->gallery->sponsorgallery->site;
		// рисуем шаблон
		$this->render('paysite', array(
			"model" => $paysite,
			"controller" => $controller,
			"section_name" => $controller->image->gallery->section->name,
			"content_type_name" => ContenttypeController::getString($controller->image->gallery->content_type),
		));
    }
 
    public function run()
    {
	}
}

?>