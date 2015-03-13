<?php

/**
 * This is the model class for table "sponsorsite".
 *
 * The followings are the available columns in table 'sponsorsite':
 * @property string $id
 * @property string $sponsor_id
 * @property string $name
 * @property string $tags
 * @property string $join_url
 * @property string $outs
 * @property double $content_rank
 */
class Sponsorsite extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Sponsorsite the static model class
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
		return 'sponsorsite';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content_rank', 'numerical'),
			array('sponsor_id, outs', 'length', 'max'=>20),
			array('name, tags, join_url', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sponsor_id, name, tags, join_url, outs, content_rank', 'safe', 'on'=>'search'),
			array('sponsor_id, name', 'required'),
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
			'feeds' => array(self::HAS_MANY, 'Sponsorfeed', 'site_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sponsor_id' => 'Sponsor',
			'name' => 'Name',
			'tags' => 'Tags',
			'join_url' => 'Join Url',
			'outs' => 'Outs',
			'content_rank' => 'Content Rank',
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
		$criteria->compare('sponsor_id',$this->sponsor_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('tags',$this->tags,true);
		$criteria->compare('join_url',$this->join_url,true);
		$criteria->compare('outs',$this->outs,true);
		$criteria->compare('content_rank',$this->content_rank);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}