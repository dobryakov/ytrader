<?php

class GalleryController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','recrop','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Gallery;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Gallery']))
		{
			$model->attributes=$_POST['Gallery'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Gallery']))
		{
			$model->attributes=$_POST['Gallery'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{

		/** @var $gallery Gallery */
		$gallery = $this->loadModel($id);

		// сначала удалим файлы
		$cropprofiles = $gallery->section->cropprofiles;
		foreach ($cropprofiles as $cropprofile) {
			foreach ($gallery->images as $image) {
				/** @var $image Image */
				// получаем путь к картинке
				$filename = $image->getThumbFilepath($cropprofile->id);
				echo ("removing image ".$filename."<br/>\n");
				if (file_exists($filename)) {
					unlink($filename);
				} else {
					echo ("file does not exists<br/>\n");
				}
				// получаем путь к flv
				$filename = $image->getFlvFilepath($cropprofile->id, $gallery->sponsorgallery->suffix ? $gallery->sponsorgallery->suffix : 'flv');
				echo ("removing video ".$filename."<br/>\n");
				if (file_exists($filename)) {
					unlink($filename);
				} else {
					echo ("file does not exists<br/>\n");
				}
			}
		}

		// отмечаем спонсорскую галерею как отклонённую
		$gallery->sponsorgallery->status = Sponsorgallery::STATUS_DECLINED;
		$gallery->sponsorgallery->save();

		// удалим тумбы
		$this->deleteGalleryThumbs($gallery);

		// потом удалим запись из БД
		$gallery->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	public function actionRecrop($id)
	{
		$gallery = $this->loadModel($id);
		if ($gallery && $gallery->sponsorgallery) {
			// удалим тумбы
			$this->deleteGalleryThumbs($gallery);
			// отметим спонсорскую галерею как новую
			$gallery->sponsorgallery->status = Sponsorgallery::STATUS_NEW;
			$gallery->sponsorgallery->gallery_id = 0;
			$gallery->sponsorgallery->save();
			// удалим галерею
			$gallery->delete();
			$this->redirect(Yii::app()->createUrl('gallery/admin'));
		}
	}

	private function deleteGalleryThumbs(Gallery $gallery)
	{
		if ($gallery && $gallery->images) {
			$section = $gallery->section;
			$cropprofiles = $section->cropprofiles;
			foreach ($gallery->images as $image) {
				/** @var $image Image */
				foreach ($cropprofiles as $cropprofile) {
					@unlink($image->getThumbFilepath($cropprofile->id));
				}
				$image->delete();
			}
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Gallery');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Gallery('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Gallery']))
			$model->attributes=$_GET['Gallery'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Gallery::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='gallery-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
