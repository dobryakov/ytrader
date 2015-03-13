<?php

/**
 * This is the model class for table "queue".
 *
 * The followings are the available columns in table 'queue':
 * @property string $number
 * @property string $data
 * @property string $t
 */
class Queue extends CActiveRecord
{

	const GALLERIES_TO_GRAB = 1;
	const IMAGES_TO_GRAB = 2;
	const IMAGES_TO_CROP = 3;
	const GALLERIES_TO_PARSE = 4;
	const GALLERIES_TO_GETMETA = 5;
	//const IMAGES_TO_DOWNLOAD = 6;
	const EMBEDS_TO_GRAB = 7;
	const IMAGE_INC_SHOWS = 8;
	const IMAGE_INC_CLICKS = 9;

	public static function push($number, $data, $comment = '')
	{
		$item = new self;
		$item->number = $number;
		$item->comment = $comment;
		$item->data = serialize($data);
		//Yii::trace("Queue::push ".$number." ".$item->data."\n");
		$item->t = time();
		$item->id = rand(1, 1000000000);
		$item->save();
		unset($item);
	}

	public static function pushUnique($number, $data, $comment = '')
	{
		if (!self::seek($number, $data)) {
			self::push($number, $data, $comment);
		}
	}

	public static function pull($number, $delete = true)
	{

		/*$item = self::model()->find("number=".intval($number)." ORDER BY t ASC");
		if ($item) {
			//Yii::trace("Queue::pull ".$number." ".$item->data."\n");
			$item->data = unserialize($item->data);
			if ($delete) {
				$item->delete();
			}
			return $item->data;
		}*/

		$item = Yii::app()->db->createCommand()
			->select('data, id')
			->from('queue q')
			->where('number='.intval($number))
			->order('t ASC')
			->limit(1)
			->queryRow();

		if ($item) {
			if ($delete) { Yii::app()->db->createCommand()->delete('queue', 'id=:id', array(':id'=>$item['id'])); }
			return unserialize($item['data']);
		}

	}

	// проверяет, есть ли уже в очереди элемент с такими данными, чтобы не дублировать его
	public static function seek($number, $data)
	{
		return self::model()->find("number=".intval($number)." AND data='".serialize($data)."'");
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Queue the static model class
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
		return 'queue';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('number, t', 'length', 'max'=>20),
			array('data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('number, data, t', 'safe', 'on'=>'search'),
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
			'number' => 'Number',
			'data' => 'Data',
			't' => 'Time',
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

		$criteria->compare('number',$this->number,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('t',$this->t,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}