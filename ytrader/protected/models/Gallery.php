<?php

/**
 * This is the model class for table "gallery".
 *
 * The followings are the available columns in table 'gallery':
 * @property string $id
 * @property string $section_id
 * @property string $sponsorgallery_id
 */
class Gallery extends CActiveRecord
{
	
	const STATUS_NEW = 0;			// только что созданная
	const STATUS_GRABBING = 5;		// начали грабить галеру
	const STATUS_GRABBED = 10;		// галера успешно сграблена
	const STATUS_CROPPING = 15;		// начали кропить галеру
	const STATUS_CROPPED = 20;		// галера успешно скроплена
	const STATUS_BAD = 90;			// плохая, например что-то не скачалось

    const SHOW_STATUS_NEW = 0;
    const SHOW_STATUS_ROTATED = 10;
    const SHOW_STATUS_OLD = 20;

	private $_thumbs = array();

	//public $CURRENT_SECTION;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Gallery the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gallery';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('section_id, sponsorgallery_id', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, section_id, sponsorgallery_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'section' => array(self::BELONGS_TO, 'Section', 'section_id'),
			'sponsorgallery' => array(self::HAS_ONE, 'Sponsorgallery', 'gallery_id'),
			'images' => array(self::HAS_MANY, 'Image', 'gallery_id'),
			'bestimage' => array(self::HAS_ONE, 'Image', 'gallery_id',
				'order' => '(clicks/(shows+1)) DESC',
				'condition' => 'status='.Image::STATUS_CROPPED,
			),
			'croppedimages' => array(self::HAS_MANY, 'Image', 'gallery_id',
				'condition' => 'status='.Image::STATUS_CROPPED,
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'section_id' => 'Section',
			'sponsorgallery_id' => 'Sponsorgallery',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('section_id',$this->section_id,true);
		$criteria->compare('sponsorgallery_id',$this->sponsorgallery_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getThumbs()
	{

		$cropprofile = Cropprofile::model()->cache(DEFAULT_CACHE_TIME)->find("section_id=".$this->section_id." AND assignment & ".Cropprofile::ASSIGN_FACE);
		if ($cropprofile) {

			$result = array();
			foreach ($this->images as $image) {
				$thumb = array();
				$thumb['url_path'] = '/thumbs/'.$this->id.'/'.$cropprofile->id.'/'.$image->id.'.jpg';
				$thumb['image_id'] = $image->id;
				$result[] = $thumb;
			}
			return $result;

		}

	}

	public function getBestThumb($cropprofile_id = null)
	{
		if (!$cropprofile_id) {
			$cropprofile = Cropprofile::model()->find("section_id=".$this->section_id." AND assignment & ".Cropprofile::ASSIGN_GALLERY);
			$cropprofile_id = $cropprofile->id;
		}
		if ($this->bestimage) {
			return
			'<a class="'.Cropprofile::getString($cropprofile_id).'_thumb_link"
			href="'.Yii::app()->createUrl('page/index', array('id'=>$this->id, "image_id"=>$this->bestimage->id)).'">
			<img class="gallery_thumb" src="'.$this->bestimage->getThumbpath($cropprofile_id).'" />
			</a>';
		}
	}

	/**
	 * Итератор, возвращает каждый раз следующую тумбу
	 * @param int $cropprofile_id
	 * @return string
	 */
	public function getNextThumb($cropprofile_id = null)
	{
		if (!$cropprofile_id) {
			$cropprofile = Cropprofile::model()->find("section_id=".$this->section_id." AND assignment & ".Cropprofile::ASSIGN_GALLERY);
			$cropprofile_id = $cropprofile->id;
		}
		/** @var $image Image */
		$thumb = false;
		if (!$this->_thumbs) {
			$key = Cropprofile::getString($cropprofile_id).'_thumbs_'.$this->id;
			$this->_thumbs = Yii::app()->cache->get($key);
			if (!$this->_thumbs) {
				$this->_thumbs = array();
				//$cropprofile = Cropprofile::model()->cache(DEFAULT_CACHE_TIME)->find("section_id=".$this->section_id." AND assignment & ".$cropprofile_id);
				if (true) {

					// если у галереи нет откропленных тумб - в рекроп
					if (!$this->croppedimages) {
						$this->enqueueRecrop('gallery '.$this->id.' has no cropped images');
					}

					foreach ($this->croppedimages as $image) {
						$this->_thumbs[$image->id]['id'] = $image->id;
						$this->_thumbs[$image->id]['url_path'] =  Controller::getStaticBase().'/thumbs/'.$this->id.'/'.$cropprofile_id.'/'.$image->id.'.jpg'; // это быстрее чем $image->getThumbpath($cropprofile_id); //'/thumbs/'.$this->id.'/'.$cropprofile->id.'/'.$image->id.'.jpg';
						$this->_thumbs[$image->id]['file_path'] = Yii::app()->basePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'thumbs'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.$cropprofile_id.DIRECTORY_SEPARATOR.$image->id.'.jpg'; // это быстрее чем $image->getThumbFilepath($cropprofile_id);
						if (!file_exists($this->_thumbs[$image->id]['file_path'])) {
							if (!PRODUCTION) { echo ("file ".$this->_thumbs[$image->id]['file_path']." not found, recrop!<br>"); }
							// ставим галерею в рекроп
							$this->enqueueRecrop('image '.$image->id.' by cropprofile '.$cropprofile_id.' file not found');
						}
					}
					$thumb = reset($this->_thumbs);
				}
				Yii::app()->cache->set($key, $this->_thumbs, DEFAULT_CACHE_TIME);
			}
		}
		if (!$thumb) {
			$thumb = next($this->_thumbs);
		}
        if ($thumb) {

			return
			'<a target="'.TARGET.'" class="'.Cropprofile::getString($cropprofile_id).'_thumb_link" href="'.Yii::app()->createUrl('page/index', array('id'=>$this->id, "image_id"=>$thumb['id'])).'"><img class="gallery_thumb" src="'.$thumb['url_path'].'" /></a>';

        } else {

			// отправляем галерею в рекроп?
			// $this->enqueueRecrop();

		}
	}

	function enqueueRecrop($reason = '')
	{
		if ($this->id) {
			// удаляем изображения
			Yii::app()->db->createCommand()
				->delete('image', 'gallery_id=:id', array(':id'=>$this->id));
			// ставим в очередь
			Queue::pushUnique(Queue::GALLERIES_TO_GRAB, $this->id, $reason);
		}
	}

	function tagUser()
	{
		$name = $this->sponsorgallery->name;
		$name = preg_replace("|\.,!?;\-:|", ' ', $name);
		if ($name) {
			$nametags = explode(" ", $name);
			//$usertags = isset($_COOKIE['ytrader-nametags']) ? unserialize($_COOKIE['ytrader-nametags']) : array();
			$_useragent = isset($_SERVER['USER_AGENT']) ? $_SERVER['USER_AGENT'] : 'undefined';
			$_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'undefined';
			$_ip = Controller::getRemoteAddr();
			$key = md5($_useragent.$_language.$_ip);
			$usertags = Yii::app()->cache->get($key);
			if (!$usertags) {
				$usertags = array();
			}
			foreach ($nametags as $k=>$v) {
				if (mb_strlen($v) < 3) {
					unset($nametags[$k]);
				} else {
					if (!in_array($v, $usertags)) {
						$usertags[] = $v;
					}
				}
			}
			Yii::app()->cache->set($key, $usertags, DEFAULT_USERTAGS_CACHE);
			//var_dump($usertags);
			//var_dump($_COOKIE);
			//print_r($nametags);
			//print_r($usertags);
			//$usertags = array_merge($nametags, $usertags);
			//setcookie('ytrader-nametags', serialize($usertags));
		}
	}

	function getUsertags()
	{
		$_useragent = isset($_SERVER['USER_AGENT']) ? $_SERVER['USER_AGENT'] : 'undefined';
		$_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'undefined';
		$_ip = Controller::getRemoteAddr();
		$key = md5($_useragent.$_language.$_ip);
		return Yii::app()->cache->get($key);
	}

	public function canonicalUrl($site)
	{
		return 'http://' . $site->host . Yii::app()->createUrl("gallery/index", array('id'=>$this->id));
	}

	/*public function getRank()
	{
		return ($this->bestimage->clicks)/(1+$this->bestimage->shows);
	}*/

}