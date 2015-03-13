<?php

/**
 * This is the model class for table "section".
 *
 * The followings are the available columns in table 'section':
 * @property string $id
 * @property string $site_id
 * @property integer $content_type
 * @property string $tags
 */
class Section extends CActiveRecord
{

	private $current_gallery_id = 0;
	private $_galleries;
	private $cropprofile;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Section the static model class
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
		return 'section';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('site_id', 'required'),
			array('content_type', 'numerical', 'integerOnly'=>true),
			array('site_id', 'length', 'max'=>20),
			array('tags', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, site_id, content_type, tags', 'safe', 'on'=>'search'),
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
			'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
			'cropprofiles' => array(self::HAS_MANY, 'Cropprofile', 'section_id'),
			'traders' => array(self::HAS_MANY, 'Trader', 'section_id'),
			'galleries' => array(self::HAS_MANY, 'Gallery', 'section_id',
				'order' => '`show_status` ASC, `rank` DESC, `id` DESC',
				'condition' => 'crop_status = '.Gallery::STATUS_CROPPED,
			),
		);
	}

	/**
	 * @return array customized attribute labels (name => label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'site_id' => 'Site',
			'content_type' => 'Content Type',
			'tags' => 'Tags',
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
		$criteria->compare('site_id',$this->site_id,true);
		$criteria->compare('content_type',$this->content_type);
		$criteria->compare('tags',$this->tags,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getSitename()
	{
		return $this->site->name;
	}

	/*
	public function getThumbs($cropprofile_id, $limit = 200)
	{
		$result = array();
		$count = 0;
		foreach ($this->galleries as $gallery) {
			$image = Image::model()->find("gallery_id=".$gallery->id.' ORDER BY (clicks/(shows+1)) DESC');
			if ($image) {
				$image->shows = $image->shows + 1;
				$image->save();
				$thumb = array();
				$thumb['image_id'] = $image->id;
				$thumb['gallery_id'] = $gallery->id;
				$thumb['url_path'] = '/thumbs/'.$gallery->id.'/'.$cropprofile_id.'/'.$image->id.'.jpg';
				$result[] = $thumb;
			}
			$count++;
			if ($count>=$limit) {
				break;
			}
		}
		return $result;
	}
	*/

	public function getNextThumb()
	{
		/*if (!$this->sorted_thumbs) {
			$this->sorted_thumbs = $this->getThumbs();
		}
		reset($this->sorted_thumbs);
		return next($this->sorted_thumbs);*/

		if (!$this->cropprofile) {
			$this->cropprofile = Cropprofile::model()->cache(DEFAULT_CACHE_TIME)->find("section_id=".$this->id." AND assignment & ".Cropprofile::ASSIGN_FACE);
		}

		//var_dump($this->id);
		//var_dump($this->cropprofile->id);

		if ($this->cropprofile) {

			if (!$this->_galleries) {
				$this->_galleries = $this->galleries;
				$gallery = reset($this->_galleries);
			} else {
				$gallery = next($this->_galleries);
			}

			if (!$gallery) {
				return;
			}

			// TODO: вот этот кусок кода сильно замедляет генерацию страницы, переписать
			/*$ci = $gallery->croppedimages;
			while(!$ci) {
				$gallery = next($this->_galleries);
				if ($gallery) {
					$ci = $gallery->croppedimages;
				} else {
					return;
				}
			}*/

			if ($gallery) {

				if ($gallery->show_status == Gallery::SHOW_STATUS_NEW) {
					// берём рандомную тумбу
					// без кэширования, чтобы она действительно была каждый раз разной
					$image = Image::model()->find("status=".Image::STATUS_CROPPED." AND gallery_id=".$gallery->id.' ORDER BY RAND()');
				} else {
					// берём лучшую тумбу
					$image = Image::model()->cache(DEFAULT_CACHE_TIME)->find("status=".Image::STATUS_CROPPED." AND gallery_id=".$gallery->id.' ORDER BY (clicks/(shows+1)) DESC');
				}

				if ($image) {
					/** @var $image Image */
					$image->incShows();
					return $image;
				}
			}
		}
	}

	public function canonicalUrl($site)
	{
		return 'http://' . $site->host . Yii::app()->createUrl("section/view", array('id'=>$this->name));
	}

}