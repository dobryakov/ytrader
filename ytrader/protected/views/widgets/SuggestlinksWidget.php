<?php

/**
 * Виджет рекомендуемых галерей, подбираемых по интересам пользователя,
 * которые он "насобирал" в interestTracker, путешествуя по нашим сайтам
 */
class SuggestlinksWidget extends CWidget
{

	public $rows;
	public $cols;
	public $content_type;

	const MIN_USERTAGS_COUNT = 5;
	//const MIN_GALLERIES_TO_SHOW = 3;

    public function init()
    {

		$min_galleries_to_show = PRODUCTION ? $this->rows * $this->cols : -1;

		$controller = $this->owner;

		// считываем ярлычки
		// TODO: пока закомментировано, перевести полностью на трекер интересов
		//$usertags = $controller->image->gallery->getUsertags();

		// считываем из трекера интересов
		$usertags = array_keys(Interest::get($controller->CURRENT_VISITOR));

		if (!is_array($usertags)) {
			$usertags = array();
		}

		// true - показываем содержимое всегда, а не только когда набрались ярлычки
		// если ярлычков недостаточно - дополняем рандомными галереями
		if (true) {

			$galleries = array();

			// подбираем контент
			foreach ($usertags as $tag) {
				$tag = mysql_escape_string($tag);

				$gallery = Gallery::model()->cache(DEFAULT_CACHE_TIME)->find(
					"
					content_type=".$this->content_type." AND
					site_id=".$controller->CURRENT_SITE->id." AND
					crop_status = ".Gallery::STATUS_CROPPED." AND
					(name LIKE '%".$tag."%' OR description LIKE '%".$tag."%') ORDER BY RAND()
					"
				);

				if ($gallery) {
					$galleries[$gallery->id] = $gallery;
				}

				// если уже набрали достаточно галерей - останавливаемся
				if (count($galleries) >= ($this->rows * $this->cols)) {
					break;
				}
			}

			// если набралось мало галерей - дополняем случайными
			$prevent_loop = 0;
			while (count($galleries) <= ($this->rows * $this->cols)) {

				$_galleries = Gallery::model()->cache(DEFAULT_CACHE_TIME)->findAll(
					"
					content_type=".$this->content_type." AND
					site_id=".$controller->CURRENT_SITE->id." AND
					crop_status = ".Gallery::STATUS_CROPPED."
					ORDER BY RAND()
					LIMIT ".($this->rows * $this->cols)."
					"
				);
				foreach ($_galleries as $gallery)
				{
					$galleries[$gallery->id] = $gallery;
				}

				// защита от бесконечной рекурсии
				$prevent_loop++;
				if ($prevent_loop > ($this->rows * $this->cols)) {
					break;
				}
			}

			// и в итоге, если галерей набралось достаточно много
			if ($galleries && count($galleries) >= ($min_galleries_to_show)) {
				// рисуем шаблон
				$this->render('suggestlinks', array(
					"galleries" => $galleries,
					"controller" => $controller,
					"rows" => $this->rows,
					"cols" => $this->cols,
				));
			}

		}

    }
 
    public function run()
    {
	}
}

?>