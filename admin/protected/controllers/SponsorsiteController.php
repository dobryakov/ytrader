<?php

class SponsorsiteController extends Controller
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
				'actions'=>array('admin','delete','import'),
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
		$model=new Sponsorsite;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Sponsorsite']))
		{
			$model->attributes=$_POST['Sponsorsite'];
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

		if(isset($_POST['Sponsorsite']))
		{
			$model->attributes=$_POST['Sponsorsite'];
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

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Sponsorsite');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

    public function actionImport()
    {

        $sponsorsite_id = isset($_REQUEST['sponsorsite_id']) ? intval($_REQUEST['sponsorsite_id']) : '';
        if ($sponsorsite_id) {
            $sponsorsite = Sponsorsite::model()->find("id=".$sponsorsite_id);
        } else {
            $sponsorsite = new Sponsorsite();
        }

        $result = null;

        if (isset($_REQUEST['sponsorsite_galleries'])) {

            if (!$sponsorsite->id) {
                throw new Exception();
            }

            // импортируем галереи
            $galleries = explode("\n", $_REQUEST['sponsorsite_galleries']);
            if (is_array($galleries) && $galleries) {
                foreach ($galleries as $gallery) {
                    $gallery = trim($gallery);
                    $p = parse_url($gallery);
                    if (isset($p['host']) && $p['host'] && isset($p['path']) && $p['path'] && isset($p['scheme']) && $p['scheme']) {
                        // галерея в принципе валидная
                        // ищем её в базе
                        $g = Sponsorgallery::model()->find("url='".mysql_escape_string($gallery)."'");
                        if (!$g) {
                            // не нашли - импортируем
                            $g = new Sponsorgallery;
                            $g->url = $gallery;
                            $g->tags = trim($_REQUEST['sponsorsite_tags']) ? trim($_REQUEST['sponsorsite_tags']) : $sponsorsite->tags;
                            $g->site_id = $sponsorsite->id;
                            $g->content_type = intval($_REQUEST['sponsorsite_contenttype']);
                            $g->save();
                            $result.="imported ".$gallery." with id ".$g->id."<br/>\n";
                        }
                    }
                }
            }
        }

        // отрисовываем форму
        $this->render('import', array(
            'sponsorsite_id' => $sponsorsite->id,
            'sponsorsite_name' => $sponsorsite->name,
            'result' => $result,
            'content_type' => isset($_REQUEST['sponsorsite_contenttype']) ? intval($_REQUEST['sponsorsite_contenttype']) : null,
        ));

    }

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Sponsorsite('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Sponsorsite']))
			$model->attributes=$_GET['Sponsorsite'];

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
		$model=Sponsorsite::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='sponsorsite-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
