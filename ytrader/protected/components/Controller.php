<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/default';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

    // дефолтные значения сайта и секции
    public $CURRENT_SITE = -1;
    public $CURRENT_SECTION = -1;
    public $DEFAULT_SKIM = 60;
	public $CURRENT_VISITOR = null;

	public $canonicalUrl;

    public function __construct($id, $module = null)
    {
        // устанавливаем текущий сайт
        $this->CURRENT_SITE = Site::model()->cache(DEFAULT_CACHE_TIME)->find("host='".$_SERVER['HTTP_HOST']."'");

		// устанавливаем дефолтный layout
		$this->layout = '//layouts/'.$this->CURRENT_SITE->id.'/default';

        if (!$this->CURRENT_SITE) {
            throw new CHttpException(403);
        }

		// проверяем отметку visitor, если нет - ставим
		$visitor = null;
		if (isset($_COOKIE['ytrader'])) {
			// ищем в БД
			$visitor = Visitor::model()->findByAttributes(array('hash' => $_COOKIE['ytrader']));
		}
		if (!$visitor) {
			$visitor = new Visitor();
			$visitor->hash = md5($this->getRemoteAddr().time().rand().uniqid());
			$visitor->first_visit_time = time();
			$visitor->entrance_site_id = $this->CURRENT_SITE->id;
			$visitor->entrance_url = $_SERVER['REQUEST_URI'];
			$visitor->save();
			setcookie('ytrader', $visitor->hash);
		}
		$this->CURRENT_VISITOR = $visitor;

        // вызываем родительский метод
        parent::__construct($id, $module);
    }

	/*public function actionError()
	{
		var_dump($_REQUEST);
	}*/

    static function getRemoteAddr()
    {
        $result = false;
        if (isset($_SERVER['REMOTE_ADDR'])) {
            if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
                if (PHP_OS !== 'Linux') {
                    $result = $_SERVER['REMOTE_ADDR'];
                } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP']) {
                    $result = $_SERVER['HTTP_X_REAL_IP'];
                }
            } else {
                $result = $_SERVER['REMOTE_ADDR'];
            }
        }
        return $result;
    }

    /**
     * Возвращает URL сервера статики
     * TODO: сделать так, чтобы этот URL зависел от текущего сайта, а так же от страны посетителя (geoip)
     * @return string
     */
    static function getStaticBase()
    {
        return 'http://static' . (STATIC_HOST_MAX_NUMBER ? intval(rand(1, STATIC_HOST_MAX_NUMBER)) : '') . '.' . str_replace('www.','',$_SERVER['HTTP_HOST']);
    }
	
}