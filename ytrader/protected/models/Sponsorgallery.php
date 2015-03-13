<?php

/**
 * This is the model class for table "sponsorgallery".
 *
 * The followings are the available columns in table 'sponsorgallery':
 * @property string $id
 * @property string $site_id
 * @property string $url
 * @property string $tags
 * @property string $name
 * @property string $description
 */
class Sponsorgallery extends CActiveRecord
{

	const STATUS_NEW = 0; 			// только что созданная
	const STATUS_ATTACHED = 110; 	// присоединена к нашей галерее
	const STATUS_BAD = 210; 		// плохая, например что-то не скачалось
	const STATUS_DECLINED = 220; 	// отклонена, запрещена по каким-либо причинам

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Sponsorgallery the static model class
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
		return 'sponsorgallery';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('site_id', 'length', 'max'=>20),
			array('url, tags, name, description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, site_id, url, tags, name, description', 'safe', 'on'=>'search'),
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
			'site' => array(self::BELONGS_TO, 'Sponsorsite', 'site_id'),
			'gallery' => array(self::HAS_ONE, 'Gallery', 'sponsorgallery_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'site_id' => 'Site',
			'url' => 'Url',
			'tags' => 'Tags',
			'name' => 'Name',
			'description' => 'Description',
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
		$criteria->compare('url',$this->url,true);
		$criteria->compare('tags',$this->tags,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getSitename()
	{
		return $this->site->name;
	}
}