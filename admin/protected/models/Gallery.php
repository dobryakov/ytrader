<?php

/**
 * This is the model class for table "gallery".
 *
 * The followings are the available columns in table 'gallery':
 * @property string $id
 * @property string $section_id
 * @property string $sponsorgallery_id
 * @property integer $crop_status
 * @property integer $show_status
 * @property string $t_create
 * @property double $rank
 */
class Gallery extends CActiveRecord
{
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
			array('crop_status, show_status', 'numerical', 'integerOnly'=>true),
			array('rank', 'numerical'),
			array('section_id, sponsorgallery_id, t_create', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, section_id, sponsorgallery_id, crop_status, show_status, t_create, rank', 'safe', 'on'=>'search'),
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
			'crop_status' => 'Crop Status',
			'show_status' => 'Show Status',
			't_create' => 'T Create',
			'rank' => 'Rank',
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
		$criteria->compare('crop_status',$this->crop_status);
		$criteria->compare('show_status',$this->show_status);
		$criteria->compare('t_create',$this->t_create,true);
		$criteria->compare('rank',$this->rank);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}