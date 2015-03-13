<?php

class GalleryController extends Controller
{
	public $gallery;
	public $section_id;
	public $section_name;

	public function filters()
	{
		return array(
			ALLOW_LAST_MODIFIED ? array(
				'CHttpCacheFilter + index',
				'lastModified'=>Yii::app()->db->createCommand("SELECT t_create FROM {{gallery}} WHERE id = ".intval($_REQUEST['id'])." LIMIT 1")->queryScalar(),
			) : false,
		);
	}
	
	public function actionIndex($id)
	{

		// принимаем решение, отправлять ли клик на контент или в трейд
        // если реф с морды текущего сайта - 100% на контент TODO: почему?
        $send_to_trade = false;
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        if ($referer) {
            $query = parse_url($referer);
            if ($query['host'] == $this->CURRENT_SITE->host) {
                // клик с морды
                if (rand(0,100) < $this->DEFAULT_SKIM) {
                    // на контент, ничего не делаем, дальше показываем контент
                } else {
                    // в трейд
                    $send_to_trade = true;
                }
            }
        }

		if (isset($_GET['image_id'])) {
			$image = Image::model()->cache(DEFAULT_CACHE_TIME)->find("id=".intval($_GET['image_id']));
			if ($image) {
				$this->gallery = $image->gallery;
				if ($this->gallery) {
					// проверим, что галерея действительно с текущего сайта
					if ($this->gallery->section->site_id <> $this->CURRENT_SITE->id) {
						throw new CHttpException(404);
					}
					// проверим, что она соответствует переданному в запросе $id
					if ($this->gallery->id <> $id) {
						throw new CHttpException(404);
					}
					// засчитаем клик
					$image->incClicks();
					/*if ($image) {
						$image->clicks = $image->clicks + 1;
						$image->save();
					}*/
				}
			} else {
				throw new CHttpException(404);
			}
		} else {
			$this->gallery = Gallery::model()->cache(DEFAULT_CACHE_TIME)->find("id=".intval($id));
			if (!$this->gallery) {
				throw new CHttpException(404);
			}
		}

		if (!$this->gallery) {
			throw new CHttpException(404);
		}

		if (!$this->gallery->section) {
			throw new CHttpException(404);
		}

		// если ранее было принято решение отправить в трейд - отправляем и прекращаем выполнение
        if ($send_to_trade) {
            $t = TraderController::actionIndex($this->gallery->section->id);
            if ($t) {
                header("Location: ".$t);
				return;
            }
        }

		// вешаем ярлычок с тэгами, составленными из названия галереи
		// TODO: пока закомментировано, потом полностью переделать на трекинг интересов
		//$this->gallery->tagUser();

		// трекаем интерес пользователя по тэгам
		Interest::track($this->CURRENT_VISITOR, $this->gallery->sponsorgallery->tags);

		// трекаем интерес пользователя по словам из названия галереи
		if ($this->gallery->name) {
			$name = $this->gallery->name;
			$name = preg_replace("|[^a-zA-Z\s]|", '', $name);
			$words = explode(' ', $name);
			if (is_array($words) && count($words)>0) {
				$words = array_map('strtolower', $words);
				$str = join(',',$words);
				Interest::track($this->CURRENT_VISITOR, $str);
			}
		}

        // если галерея содержит всего одно изображение, то перекидываем сразу на него
        if (count($this->gallery->images)<2) {
            $a = $this->gallery->images;
            $image = $a[0];
			$page = Yii::app()->createUrl('page/index', array('id' => $this->gallery->id, 'image_id' => $image->id));
            header("Location: ".$page);
        }

        //$this->pageTitle=Yii::app()->name . ': '.$this->gallery->section->name.': '.$this->gallery->sponsorgallery->name;
		$name = trim($this->gallery->sponsorgallery->name);
		$name = trim($name, ' _-!?:;.,');
		if (mb_strlen($name)>100) { $name = mb_substr($name, 0, 100).'...'; }
        $this->pageTitle = $name ? $name : $this->CURRENT_SITE->name . ': '.$this->gallery->section->name;
		$this->canonicalUrl = $this->gallery->canonicalUrl($this->CURRENT_SITE);

		// рисуем шаблон
		// имя шаблона составляется как index - id секции - id кроппрофайла
		$cropprofile = Cropprofile::model()->cache(DEFAULT_CACHE_TIME)->find("section_id=".$this->gallery->section_id." AND assignment & ".Cropprofile::ASSIGN_GALLERY);

        $f = 'index-'.$this->gallery->section_id.'-'.$cropprofile->id;
        if (!file_exists(dirname(__FILE__).'/../views/gallery/'.$f.'.php')) {
            $f = 'index';
        }

		$this->section_id = $this->gallery->section->id;
		$this->section_name = $this->gallery->section->name;

		$this->render($f, array(
			'controller' => $this,
			'model' => $this->gallery,
			'gallery_name' => $this->gallery->sponsorgallery->name,
		));
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}