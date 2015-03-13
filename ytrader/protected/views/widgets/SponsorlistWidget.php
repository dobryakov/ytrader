<?php

/**
 * Виджет выводит список платников по заданной секции
 */
class SponsorlistWidget extends CWidget
{

	public $model;
	public $rows;
	public $cols;

    public function init()
    {
		$controller = $this->owner;

		// выбираем платники по заданной секции $model->tags
		$sites = array();
		$tags = explode(',', $this->model->tags);

		if (is_array($tags)) {
			foreach ($tags as $tag) {
				$s = Sponsorsite::model()->cache(DEFAULT_CACHE_TIME)->findAll('tags LIKE "%'.$tag.'%" ORDER BY RAND() LIMIT '.$this->rows*$this->cols);
				if (is_array($s)) {
					foreach ($s as $site) {
						$sites[$site->id] = $site;
						if (count($sites) >= $this->rows*$this->cols) { break; }
					}
				}
				if (count($sites) >= $this->rows*$this->cols) { break; }
			}
		}

		if ($sites) {
			$i = 0;
			foreach ($sites as $site) {
				// рисуем шаблон
				$this->render('sponsorlistitem', array(
					"controller" => $controller,
					"model" => $site,
				));
				$i++;
				if ($i >= $this->rows*$this->cols) { break; }
			}
		}

    }
 
    public function run()
    {
	}
}

?>