<?php

class ContenttypeController extends Controller
{
	
	// тип контента задаётся как битовая маска (1+2+4 если все типы сразу)
	const CONTENT_TYPE_IMAGES = 1;
	const CONTENT_TYPE_MOVIES = 2;
	const CONTENT_TYPE_EMBEDS = 4;
	
	public function actionIndex()
	{
		$this->render('index');
	}

    public function listTypes()
    {
        return array(self::CONTENT_TYPE_IMAGES, self::CONTENT_TYPE_MOVIES, self::CONTENT_TYPE_EMBEDS);
    }

	public static function getString($content_type)
	{
		$s = array();
		if ($content_type & self::CONTENT_TYPE_IMAGES) { $s[] = 'pics'; }
		if ($content_type & self::CONTENT_TYPE_MOVIES) { $s[] = 'movies'; }
		if ($content_type & self::CONTENT_TYPE_EMBEDS) { $s[] = 'videos'; }
		return join(', ', $s);
	}

	public static function getList()
	{
		return array(
			self::CONTENT_TYPE_IMAGES => self::getString(self::CONTENT_TYPE_IMAGES),
			self::CONTENT_TYPE_MOVIES => self::getString(self::CONTENT_TYPE_MOVIES),
			self::CONTENT_TYPE_EMBEDS => self::getString(self::CONTENT_TYPE_EMBEDS),
		);
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