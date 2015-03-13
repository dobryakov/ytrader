<?php

/**
 * This is the model class for table "cropprofile".
 *
 * The followings are the available columns in table 'cropprofile':
 * @property string $id
 * @property string $section_id
 * @property integer $width
 * @property integer $height
 */
class Cropprofile extends CActiveRecord
{
	
	const ASSIGN_FACE = 1; // кроп-профайл предназначен для морды или секции
	const ASSIGN_GALLERY = 2; // для галереи
	const ASSIGN_PAGE = 4; // для страницы "одной картинки"
	const ASSIGN_ADBLOCK = 8; // пока не используется

	public static function getString($cropprofile_id)
	{
		$str = array(
			self::ASSIGN_FACE => 'face',
			self::ASSIGN_GALLERY => 'gallery',
			self::ASSIGN_PAGE => 'page',
			self::ASSIGN_ADBLOCK => 'adblock',
		);
		if (isset($str[$cropprofile_id])) {
			return $str[$cropprofile_id];
		}
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Cropprofile the static model class
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
		return 'cropprofile';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('width, height', 'numerical', 'integerOnly'=>true),
			array('section_id', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, section_id, width, height', 'safe', 'on'=>'search'),
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
			'width' => 'Width',
			'height' => 'Height',
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
		$criteria->compare('width',$this->width);
		$criteria->compare('height',$this->height);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}