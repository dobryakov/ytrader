<?php

class SectionController extends Controller
{
	

	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $model;
	public $section_id;
	public $section_name;
	public $pageTitle;

	public function filters()
	{
		return array(
			ALLOW_LAST_MODIFIED ? array(
				'CHttpCacheFilter + view',
				'lastModified'=>Yii::app()->db->createCommand("SELECT t FROM {{section}} WHERE id = ".intval($_REQUEST['id'])." LIMIT 1")->queryScalar(),
			) : false,
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{

		// проверяем, является ли входящий визит - визитом от трейдера
		TraderController::actionIn();

		if (is_numeric($id)) {
			// получаем секцию по id
			$section = Section::model()->cache(DEFAULT_CACHE_TIME)->find("id=".intval($id));
		} else {
			// получаем секцию по имени
			$section = Section::model()->find("name = :name", array(":name" => urldecode($id)));
		}

		// проверяем, является ли текущая секция - действительно секцией данного сайта
		if (!$section) {
			throw new CHttpException(404);
			return;
		}
		if ($section->site_id <> $this->CURRENT_SITE->id) {
			throw new CHttpException(404);
			return;
		}

		// если нам был передан числовой идентификатор секции -
		// делаем редирект на символьный
		if (is_numeric($id)) {
			$this->redirect(Yii::app()->createUrl('section/view', array('id' => $section->name)));
			return;
		}

		$this->CURRENT_SECTION = $section->id;

		$f = 'view-'.$section->id;
        if (!file_exists(dirname(__FILE__).'/../views/section/'.$f.'.php')) {
            $f = 'view';
        }

		$this->model = $section;
		$this->section_id = $this->model->id;
		$this->section_name = $this->model->name;
		$this->pageTitle = $this->section_name.' '.ucfirst(ContenttypeController::getString($this->model->content_type)).' XXXX';
		$this->canonicalUrl = $this->model->canonicalUrl($this->CURRENT_SITE);

		$this->render($f,array(
			'controller' => $this,
			'model'=>$this->model,
			'host'=>$this->CURRENT_SITE->host,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Section::model()->cache(DEFAULT_CACHE_TIME)->findByPk($id);
		//$model->CURRENT_SECTION = $this->CURRENT_SECTION;
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

}
