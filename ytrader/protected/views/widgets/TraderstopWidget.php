<?php

class TraderstopWidget extends CWidget
{

	public $model;
	public $rows;
	public $cols;

	public function init()
    {
		$controller = $this->owner;

		if ($this->model->traders) {
			// рисуем шаблон
			$this->render('top', array(
				"controller" => $controller,
				"rows" => $this->rows,
				"cols" => $this->cols,
				"model" => $this->model,
				"trader_model" => Trader::model(),
			));
		}
    }
 
    public function run()
    {
	}
}

?>