<?php

class SectionController extends Controller
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
				'actions'=>array('admin','delete','recrop'),
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
		$model=new Section;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Section']))
		{
			$model->attributes=$_POST['Section'];

			if($model->save()) {

				// для удобства сразу создадим кроп-профайлы для секции, скопировав их у первой секции текущего сайта
				if ($model->site_id) {
					$first_section = Section::model()->find("site_id=".$model->site_id);
					if ($first_section) {
						$cropprofiles = Cropprofile::model()->findAll("section_id=".$first_section->id);
						foreach ($cropprofiles as $cropprofile) {
							$new_cropprofile = new Cropprofile();
							$new_cropprofile->section_id = $model->id;
							$new_cropprofile->width = $cropprofile->width;
							$new_cropprofile->height = $cropprofile->height;
							$new_cropprofile->assignment = $cropprofile->assignment;
							$new_cropprofile->save();
						}
					}
				}

				$this->redirect(array('view','id'=>$model->id));

			}

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

		if(isset($_POST['Section']))
		{
			$model->attributes=$_POST['Section'];
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
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	public function actionRecrop($id)
	{
		$model = $this->loadModel($id);
		if ($model) {
			foreach ($model->galleries as $gallery) {
				/** @var $gallery Gallery */
				// TODO: переписать нахрен, инкапсулировать методы удаления в модели, а не в контроллере
				if ($gallery && $gallery->sponsorgallery) {
					// удалим тумбы
					foreach ($gallery->images as $image) {
						$image->delete();
					}
					// отметим спонсорскую галерею как новую
					$gallery->sponsorgallery->status = Sponsorgallery::STATUS_NEW;
					$gallery->sponsorgallery->gallery_id = 0;
					$gallery->sponsorgallery->save();
					// удалим галерею
					$gallery->delete();
				}
			}
			$this->redirect(Yii::app()->createUrl('section/view', array('id'=>$id)));
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Section');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Section('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Section']))
			$model->attributes=$_GET['Section'];

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
		$model=Section::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='section-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
