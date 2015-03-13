<?php

/**
 * This is the model class for table "image".
 *
 * The followings are the available columns in table 'image':
 * @property string $id
 * @property string $gallery_id
 * @property string $source_url
 */
class Image extends CActiveRecord
{

	const RANK_MIN_CLICKS = 2; // минимальное число кликов до формирования rank, если меньше - rank 0
	const RANK_MIN_SHOWS = 5; // минимальное число показов до формирования rank, если меньше - rank 0

	public $url_path;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Image the static model class
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
		return 'image';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gallery_id', 'required'),
			array('gallery_id', 'length', 'max'=>20),
			array('source_url', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, gallery_id, source_url', 'safe', 'on'=>'search'),
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
			'gallery' => array(self::BELONGS_TO, 'Gallery', 'gallery_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'gallery_id' => 'Gallery',
			'source_url' => 'Source Url',
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
		$criteria->compare('gallery_id',$this->gallery_id,true);
		$criteria->compare('source_url',$this->source_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getSourcepath()
	{
		if ($this->id) {
			return Yii::app()->basePath.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'imagesources'.DIRECTORY_SEPARATOR.$this->id.'.jpg';
		}
	}

    public function getFlvsourcepath($suffix='flv')
    {
        if ($this->id) {
            return Yii::app()->basePath.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'flvs'.DIRECTORY_SEPARATOR.$this->id.'.'.$suffix;
        }
    }
	
	public function getRank()
	{
        if ((intval($this->shows) > self::RANK_MIN_SHOWS) && (intval($this->clicks) > self::RANK_MIN_CLICKS)) {
            return ($this->clicks + 1) / ($this->shows + 1);
		} else {
			return 0;
		}
	}

	public function incShows()
	{
		if ($this->clickDefend('show')) { return; }
		$this->shows = $this->shows + 1;
		$this->save();
	}

	public function incClicks()
	{
		if ($this->clickDefend('click')) { return; }
		$this->clicks = $this->clicks + 1;
		$this->save();
	}

	private function clickDefend($mode)
	{
		$ip = Controller::getRemoteAddr();
		$key = md5($mode.$this->id.$ip);
		if (Yii::app()->cache->get($key)) {
			// такой клик в кэше уже есть, значит его считать нельзя
			return true;
		} else {
			// записываем в кэш
			Yii::app()->cache->set($key, true, DEFAULT_CLICKS_CACHE);
		}
	}

    public function getThumbFilepath($cropprofile_id)
    {
        return Yii::app()->basePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'thumbs'.DIRECTORY_SEPARATOR.$this->gallery->id.DIRECTORY_SEPARATOR.$cropprofile_id.DIRECTORY_SEPARATOR.$this->id.'.jpg';
    }

    public function getFlvFilepath($cropprofile_id, $suffix = 'flv')
    {
        return Yii::app()->basePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'flvs'.DIRECTORY_SEPARATOR.$this->gallery->id.DIRECTORY_SEPARATOR.$cropprofile_id.DIRECTORY_SEPARATOR.$this->id.'.'.$suffix;
    }

	public function getThumbpath($cropprofile_id)
	{
		// TODO: createUrl
		return Controller::getStaticBase().'/thumbs/'.$this->gallery->id.'/'.$cropprofile_id.'/'.$this->id.'.jpg';
	}

    public function getFlvpath($cropprofile_id)
    {
        // TODO: createUrl
        $suffix = $this->gallery->sponsorgallery->suffix ? $this->gallery->sponsorgallery->suffix : 'flv';
        return Controller::getStaticBase().'/flvs/'.$this->gallery->id.'/'.$cropprofile_id.'/'.$this->id.'.'.$suffix;
    }

	public function getGallerypath()
	{
		// TODO: createUrl
		//return '/gallery/'.$this->gallery_id.'/?image_id='.$this->id;
		return Yii::app()->createUrl('gallery/index', array('id' => $this->gallery_id, 'image_id' => $this->id));
	}

	public function getPagepath()
	{
		return Yii::app()->createUrl('page/index', array('id'=>$this->gallery->id, "image_id"=>$this->id));
	}

	public function makeThumbHTML($cropprofile_id)
	{
		$thumb = array();
		$thumb['image_id'] = $this->id;
		$thumb['gallery_id'] = $this->gallery->id;
		$thumb['description'] = $this->gallery->sponsorgallery->name;
		$thumb['url_path'] = $this->getThumbpath($cropprofile_id); //'/thumbs/'.$this->gallery->id.'/'.$cropprofile_id.'/'.$this->id.'.jpg';
		// TODO: сделать через createUrl
		//$thumb['href'] = '/gallery/'.$thumb['gallery_id'].'/?image_id='.$thumb['image_id'];
		$thumb['href'] = Yii::app()->createUrl('gallery/index', array('id' => $thumb['gallery_id'], 'image_id' => $thumb['image_id']));
		//return $thumb;
		return "<a target='".TARGET."' class='section_thumb_link' title='".CHtml::encode($thumb['description'])."' href='".$thumb['href']."'><img class='section_thumb' src='".$thumb['url_path']."' border='0' alt=''/></a>";
	}

	public function canonicalUrl($site)
	{
		return 'http://' . $site->host . Yii::app()->createUrl("page/index", array('id'=>$this->gallery->id, 'image_id'=>$this->id));
	}

	// TODO:
	/**
	 * написать отдельные методы на получение file_path, url_path, thumb_file_path, thumb_url_path
	 * написать методы для инкремента clicks и views, чтобы можно было поставить клик-дефендер
	 * написать клик-дефендер, который будет сохранять в cache связку image_id && IP, чтобы предотвратить накрутку по кликам [и показам]
	 */
	
}