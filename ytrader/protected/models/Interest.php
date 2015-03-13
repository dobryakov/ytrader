<?php

/**
 * This is the model class for table "interest".
 *
 * The followings are the available columns in table 'interest':
 * @property string $id
 * @property string $hash
 * @property string $tag
 * @property string $t
 */
class Interest extends CActiveRecord
{

	/**
	 * записывает интерес пользователя
	 * к определённому контенту по тэгам
	 */
	public static function track(Visitor $visitor, $tags_string)
	{
		$tags = explode(',', $tags_string);
		if (is_array($tags) && count($tags)>0) {
			foreach ($tags as $tag) {
				if (strlen($tag) > 4) {
					$interest = new self;
					$interest->hash = $visitor->hash;
					$interest->tag = $tag;
					$interest->t = time();
					$interest->save();
				}
			}
		}
	}

	/**
	 * получает интересы пользователя в виде массива
	 * со счётчиком интересов по каждому тэгу
	 * @static
	 * @param Visitor $visitor
	 */
	public static function get(Visitor $visitor)
	{
		$result = Yii::app()->db->createCommand()
			->select('tag, COUNT(DISTINCT(id)) c')
			->from('interest i')
			->where('hash="'.$visitor->hash.'"')
			->group('tag')
			->order('c DESC')
			->limit(64)
			->queryAll();
		$tags = array();
		if (is_array($result)) {
			foreach ($result as $k=>$data) {
				$tags[$data['tag']] = $data['c'];
			}
		}
		return $tags;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Interest the static model class
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
		return 'interest';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hash', 'length', 'max'=>32),
			array('tag', 'length', 'max'=>16),
			array('t', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, hash, tag, t', 'safe', 'on'=>'search'),
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
			'hash' => 'Hash',
			'tag' => 'Tag',
			't' => 'T',
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
		$criteria->compare('hash',$this->hash,true);
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('t',$this->t,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}