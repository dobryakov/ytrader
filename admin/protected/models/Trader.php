<?php

/**
 * This is the model class for table "trader".
 *
 * The followings are the available columns in table 'trader':
 * @property string $id
 * @property string $host
 * @property string $url
 * @property string $section_id
 * @property string $daily_in
 * @property string $daily_out
 */
class Trader extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Trader the static model class
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
		return 'trader';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('section_id, daily_in, daily_out', 'length', 'max'=>20),
			array('host, url', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, host, url, section_id, daily_in, daily_out', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'host' => 'Host',
			'url' => 'Url',
			'section_id' => 'Section',
			'daily_in' => 'Daily In',
			'daily_out' => 'Daily Out',
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
		$criteria->compare('host',$this->host,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('section_id',$this->section_id,true);
		$criteria->compare('daily_in',$this->daily_in,true);
		$criteria->compare('daily_out',$this->daily_out,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}