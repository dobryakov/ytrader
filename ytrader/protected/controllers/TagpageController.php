<?php

class TagpageController extends Controller
{

	function actionView($ct, $tag)
	{

		$content_type = null;
		foreach (ContenttypeController::getList() as $k => $_ct) {
			if (strtoupper($_ct) == strtoupper($ct)) {
				$content_type = $k;
			}
		}
		if (!$content_type) {
			throw new CHttpException(404);
		}

		$tag = trim(mysql_escape_string(urldecode($tag)));

		if (!$tag) {
			throw new CHttpException(404);
		}

		// устанавливаем layout
		$this->layout = '//layouts/'.$this->CURRENT_SITE->id.'/section';

		// устанавливаем title
		$this->pageTitle = ucfirst($tag).' xxxx';

		// ключ кэша
		$key = 'tagpage_'.$content_type.'_'.$tag;

		$sponsorgalleries = Yii::app()->cache->get($key);

		if (!$sponsorgalleries) {
			$sponsorgalleries = array();
			// перебираем секции текущего сайта
			foreach ($this->CURRENT_SITE->sections as $section) {
				if ($section->content_type == $content_type) {
					$sponsorgalleries = array_merge($sponsorgalleries, Sponsorgallery::model()->cache(DEFAULT_CACHE_TIME)->findAll('gallery_id > 0 AND tags LIKE "%'.$tag.'%"'));
				}
			}
			// кладём в кэш
			Yii::app()->cache->set($key, $sponsorgalleries, DEFAULT_TAGPAGE_CACHE);
		}

		$this->render('view',array(
			'sponsorgalleries' => $sponsorgalleries,
			'tag' => $tag,
			'controller' => $this,
			'host'=>$this->CURRENT_SITE->host,
		));

	}

}

?>