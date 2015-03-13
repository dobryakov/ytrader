<?php

class SiteController extends Controller
{

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			ALLOW_LAST_MODIFIED ? array(
				'CHttpCacheFilter + index',
				'lastModified'=>Yii::app()->db->createCommand("SELECT MAX(`t`) FROM {{section}} WHERE site_id = ".$this->CURRENT_SITE->id)->queryScalar(),
			) : false,
			array(
				'COutputCache + index',
				'duration'=>0.1*DEFAULT_CACHE_TIME,
				'varyByRoute'=>true,
			),
		);
	}

	/**
	 * Главная страница сайта
	 */
	public function actionIndex()
	{

        $this->pageTitle = CHtml::encode($this->CURRENT_SITE->name);

        $this->render('index',array(
			'controller' => $this,
        ));

	}

	/**
	 * Исходящий клик на join now
	 */
	public function actionOut($id)
	{
		$site = Sponsorsite::model()->cache(DEFAULT_CACHE_TIME)->find("id=".intval($id));
		if ($site) {
			header("Location: ".$site->join_url);
			$site->incOuts();
		}
	}

}
