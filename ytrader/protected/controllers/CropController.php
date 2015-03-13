<?php

class CropController extends Controller
{

	public function actionRun($gallery_id, $cropprofile_id, $image_id)
	{
		/** @var $image Image */
		$image = Image::model()->findByPk($image_id);
		if ($image && $image->gallery_id == $gallery_id) {

			$cropprofile = Cropprofile::model()->findByPk($cropprofile_id);
			if ($cropprofile) {

				// начинаем работу

				// сохранился ли исходник?
				$source_filename = $image->getSourcepath();
				if (!file_exists($source_filename)) {
					// если нет - скачиваем
				}

				// обрабатываем

				// на локале сохраняем и выдаём
				// на продакшене просто выдаём

				if (PRODUCTION) {

				} else {

				}

			}

		}
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