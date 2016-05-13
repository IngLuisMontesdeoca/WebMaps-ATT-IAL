<?php

/**
 * This is the model class for table "dat_historico".
 *
 * The followings are the available columns in table 'dat_historico':
 * @property integer $n_historico_id
 * @property string $d_historico_fechaenvio
 * @property string $d_historico_fecharespuesta
 * @property string $d_historico_fechalocalizacion
 * @property double $d_historico_longitude
 * @property double $d_historico_latitude
 * @property integer $n_historico_radio
 * @property integer $n_tipolocalizacion_id
 * @property integer $n_handset_id
 * @property integer $n_alarma_id
 * @property integer $n_estatuslocalizacion_id
 * @property integer $n_hilo_id
 */
class DatHistorico extends CActiveRecord
{
        public $hash;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DatHistorico the static model class
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
		return 'dat_historico';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('n_handset_id, n_alarma_id, n_estatuslocalizacion_id', 'required'),
			array('n_historico_radio, n_tipolocalizacion_id, n_handset_id, n_alarma_id, n_estatuslocalizacion_id, n_hilo_id', 'numerical', 'integerOnly'=>true),
			array('d_historico_longitude, d_historico_latitude', 'numerical'),
			array('d_historico_fechaenvio, d_historico_fecharespuesta, d_historico_fechalocalizacion', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('n_historico_id, d_historico_fechaenvio, d_historico_fecharespuesta, d_historico_fechalocalizacion, d_historico_longitude, d_historico_latitude, n_historico_radio, n_tipolocalizacion_id, n_handset_id, n_alarma_id, n_estatuslocalizacion_id, n_hilo_id', 'safe', 'on'=>'search'),
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
			'n_historico_id' => 'N Historico',
			'd_historico_fechaenvio' => 'D Historico Fechaenvio',
			'd_historico_fecharespuesta' => 'D Historico Fecharespuesta',
			'd_historico_fechalocalizacion' => 'D Historico Fechalocalizacion',
			'd_historico_longitude' => 'D Historico Longitude',
			'd_historico_latitude' => 'D Historico Latitude',
			'n_historico_radio' => 'N Historico Radio',
			'n_tipolocalizacion_id' => 'N Tipolocalizacion',
			'n_handset_id' => 'N Handset',
			'n_alarma_id' => 'N Alarma',
			'n_estatuslocalizacion_id' => 'N Estatuslocalizacion',
			'n_hilo_id' => 'N Hilo',
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

		$criteria->compare('n_historico_id',$this->n_historico_id);
		$criteria->compare('d_historico_fechaenvio',$this->d_historico_fechaenvio,true);
		$criteria->compare('d_historico_fecharespuesta',$this->d_historico_fecharespuesta,true);
		$criteria->compare('d_historico_fechalocalizacion',$this->d_historico_fechalocalizacion,true);
		$criteria->compare('d_historico_longitude',$this->d_historico_longitude);
		$criteria->compare('d_historico_latitude',$this->d_historico_latitude);
		$criteria->compare('n_historico_radio',$this->n_historico_radio);
		$criteria->compare('n_tipolocalizacion_id',$this->n_tipolocalizacion_id);
		$criteria->compare('n_handset_id',$this->n_handset_id);
		$criteria->compare('n_alarma_id',$this->n_alarma_id);
		$criteria->compare('n_estatuslocalizacion_id',$this->n_estatuslocalizacion_id);
		$criteria->compare('n_hilo_id',$this->n_hilo_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        public function locationHistory()
        {
            //$this->hash = "2";
            $sql = 'SELECT 
                        longitude,
                        latitude,
                        fecha
                    FROM
                        (SELECT 
                            SHA1(MD5(CONCAT(n_alarma_id, "'.Yii::app()->params['semillaAlarma'].'"))) hashAlarma,
                                n_alarma_id idAlarma,
                                d_historico_longitude longitude,
                                d_historico_latitude latitude,
                                d_historico_fechalocalizacion fecha,
                                n_historico_id
                        FROM
                            NXT_IALARM.dat_historico ORDER BY n_historico_id ASC) historico
                    WHERE
                    hashAlarma = "'.$this->hash.'"
                    AND latitude != ""
                    AND longitude != ""
                    ORDER BY fecha DESC';
            
            //'ed4663389aded395ac5e0393a190e7d41d897ef2', '1', NULL, NULL
            //'de770c27ac07ad106f5d8ade534f3ce6000e3ba0', '2', '-99.227508333333', '19.638711666667'

            //var_dump($sql);die();
            if(count(Yii::app()->db->createCommand($sql)->queryAll())>0)
                return Yii::app()->db->createCommand($sql)->queryAll();
            else
                return 0;
        }
        
}