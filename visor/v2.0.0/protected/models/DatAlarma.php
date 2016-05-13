<?php

/**
 * This is the model class for table "dat_alarma".
 *
 * The followings are the available columns in table 'dat_alarma':
 * @property integer $n_alarma_id
 * @property string $d_alarma_fechainicio
 * @property string $d_alarma_fechafin
 * @property string $d_alarma_fechaultimoreporte
 * @property double $d_alarma_longitude
 * @property double $d_alarma_latitude
 * @property integer $n_alarma_radio
 * @property integer $n_handset_id
 * @property integer $n_estatus_id
 */
class DatAlarma extends CActiveRecord
{
        public $hash;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DatAlarma the static model class
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
		return 'dat_alarma';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('d_alarma_fechainicio, n_handset_id, n_estatus_id', 'required'),
			array('n_alarma_radio, n_handset_id, n_estatus_id', 'numerical', 'integerOnly'=>true),
			array('d_alarma_longitude, d_alarma_latitude', 'numerical'),
			array('d_alarma_fechafin, d_alarma_fechaultimoreporte', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('n_alarma_id, d_alarma_fechainicio, d_alarma_fechafin, d_alarma_fechaultimoreporte, d_alarma_longitude, d_alarma_latitude, n_alarma_radio, n_handset_id, n_estatus_id', 'safe', 'on'=>'search'),
		);
	}

        //valida hash
        public function isHashAlarma()
        {
            if((ctype_xdigit($this->hash)) && (strlen($this->hash)==40))
                return true;
            else
                return false;
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
			'n_alarma_id' => 'N Alarma',
			'd_alarma_fechainicio' => 'D Alarma Fechainicio',
			'd_alarma_fechafin' => 'D Alarma Fechafin',
			'd_alarma_fechaultimoreporte' => 'D Alarma Fechaultimoreporte',
			'd_alarma_longitude' => 'D Alarma Longitude',
			'd_alarma_latitude' => 'D Alarma Latitude',
			'n_alarma_radio' => 'N Alarma Radio',
			'n_handset_id' => 'N Handset',
			'n_estatus_id' => 'N Estatus',
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

		$criteria->compare('n_alarma_id',$this->n_alarma_id);
		$criteria->compare('d_alarma_fechainicio',$this->d_alarma_fechainicio,true);
		$criteria->compare('d_alarma_fechafin',$this->d_alarma_fechafin,true);
		$criteria->compare('d_alarma_fechaultimoreporte',$this->d_alarma_fechaultimoreporte,true);
		$criteria->compare('d_alarma_longitude',$this->d_alarma_longitude);
		$criteria->compare('d_alarma_latitude',$this->d_alarma_latitude);
		$criteria->compare('n_alarma_radio',$this->n_alarma_radio);
		$criteria->compare('n_handset_id',$this->n_handset_id);
		$criteria->compare('n_estatus_id',$this->n_estatus_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        public function locationAlert()
        {
            //$this->hash = "2";
            $sql = 'SELECT 
                        alarma.longitude longitude,
                        alarma.latitude latitude
                    FROM
                        (SELECT 
                            SHA1(MD5(CONCAT(n_alarma_id, "'.Yii::app()->params['semillaAlarma'].'"))) hashAlarma,
                                d_alarma_longitude longitude,
                                d_alarma_latitude latitude
                        FROM
                            NXT_IALARM.dat_alarma
                        WHERE
                            (n_estatus_id = "1") OR (n_estatus_id = "9")) alarma
                    WHERE
                    hashAlarma = "'.$this->hash.'"';
            
            //var_dump($sql);die();
            if(count(Yii::app()->db->createCommand($sql)->queryAll())>0)
                return Yii::app()->db->createCommand($sql)->queryAll();
            else
                return 0;
        }                
}