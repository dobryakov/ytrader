<?php

/**
 * This is the model class for table "trader".
 *
 * The followings are the available columns in table 'trader':
 * @property string $id
 * @property string $host
 * @property string $url
 * @property string $section_id
 */
class Trader extends CActiveRecord
{

	private $_traders;

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
			array('section_id', 'length', 'max'=>20),
			array('host, url', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, host, url, section_id', 'safe', 'on'=>'search'),
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
			'host' => 'Host',
			'url' => 'Url',
			'section_id' => 'Section',
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getTopTrader($section_id)
    {
        // TODO: написать реальный алгоритм выбора топового трейдера для данного клика
        $traders = $this->model()->cache(DEFAULT_CACHE_TIME)->findAll("section_id=".intval($section_id)." ORDER BY (daily_in+1)/(daily_out+1) DESC");
        foreach ($traders as $trader) {
            $out = Out::model()->find("trader_id=".$trader->id." AND ip=".ip2long(Controller::getRemoteAddr()));
            if (!$out) {
                // этому трейдеру мы ещё не отправляли, поэтому отправляем
                return $trader;
            }
        }
    }

	public function getNextTrader($section_id)
	{
		if (!$this->_traders) {
			$this->_traders = $this->model()->cache(DEFAULT_CACHE_TIME)->findAll("section_id=".intval($section_id)." ORDER BY (daily_in+1)/(daily_out+1) DESC");
			$trader = reset($this->_traders);
			return $trader;
		}
		return next($this->_traders);
	}

}