<?php

class PageController extends Controller
{
	public $gallery;
	public $image;
	public $model;
	public $section_id;
	public $section_name;

	// TODO: сделать зависимость кэша от IP, чтобы не кэшировались "наши" ссылки
	public function filters()
	{
		return array(
			ALLOW_LAST_MODIFIED ? array(
				'CHttpCacheFilter + index',
				'lastModified'=>Yii::app()->db->createCommand("SELECT t_create FROM {{gallery}} WHERE id = ".intval($_REQUEST['id'])." LIMIT 1")->queryScalar(),
			) : false,
		);
	}

	public function actionIndex()
	{

		// если не указано изображение - 404
		if (!isset($_GET['image_id']) || intval($_GET['image_id'])<1 || !isset($_GET['id']) || intval($_GET['id'])<1)
		{
			// TODO: 404 exception
			header("Location: /");
		}

		$this->image = Image::model()->cache(DEFAULT_CACHE_TIME)->find("id=".intval($_GET['image_id']));
		if ($this->image) {
			$this->gallery = $this->image->gallery;
			if ($this->gallery) {

				// проверим, что галерея действительно с текущего сайта
				if ($this->gallery->section->site->id <> $this->CURRENT_SITE->id) {
					// TODO: 404 exception
					header("Location: /");
				}

				// проверим, что галерея в URL соответствует изображению
				if (intval($this->gallery->id) !== intval($_GET['id'])) {
					// TODO: 404 exception
					header("Location: /");
				}

				// трекаем интерес пользователя
				Interest::track($this->CURRENT_VISITOR, $this->image->gallery->tags);

				// засчитаем показ изображения
				$this->image->incShows();

                $this->pageTitle = $this->image->gallery->section->name .': '.trim(ContenttypeController::getString($this->image->gallery->content_type),'s').' #'. $this->image->id;
				$this->canonicalUrl = $this->image->canonicalUrl($this->CURRENT_SITE);

				// рисуем шаблон
				// имя составляется как index - id секции - id кроппрофайла
				$cropprofile = Cropprofile::model()->cache(DEFAULT_CACHE_TIME)->find("section_id=".$this->gallery->section_id." AND assignment & ".Cropprofile::ASSIGN_PAGE);

                $f = 'index-'.$this->gallery->section_id.'-'.$cropprofile->id;
                if (!file_exists(dirname(__FILE__).'/../views/page/'.$f.'.php')) {
                    $f = 'index';
                }

				$this->model = $this->image;
				$this->section_id = $this->image->gallery->section->id;
				$this->section_name = $this->image->gallery->section->name;

				$this->render($f, array(
					"model" => $this->model,
				));

			}
		}
	}

}