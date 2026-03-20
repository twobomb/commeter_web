<?php

namespace app\models;

use Yii;
use yii\base\BaseObject;

/**
 * This is the model class for table "feature_values".
 *
 * @property int $id
 * @property string|null $value
 * @property int|null $feature_id
 * @property string $item_id
 *
 * @property Feature $feature
 * @property Item $item
 */
class FeatureValue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'feature_values';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'string'],
            [['feature_id','item_id'], 'integer'],
            [['item_id'], 'required'],
            [['feature_id'], 'exist', 'skipOnError' => true, 'targetClass' => Feature::class, 'targetAttribute' => ['feature_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::class, 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',
            'feature_id' => 'Feature ID',
            'item_id' => 'Item ID',
        ];
    }
    public function getDisplayValue(){
        $val = $this->value;
        /*
         *       "string"=>"Строка",
          "text"=>"Многострочное текстовое поле",
          "int"=>"Целое число",
          "double"=>"Дробное число",
          "list"=>"Список из словаря",
          "date"=>"Дата",
          "bool"=>"Булево значение(да\нет)"*/
        switch ($this->feature->type){
            case "list":
                if($this->feature->dictinary_id != null) {
                    $di = DictinaryItem::findOne(["id"=>$this->value]);
                    if($di  != null)
                        $val = $di->value;
                }
                break;
            case "date":
                if($this->value != "")
                    $val = Yii::$app->formatter->asDate($this->value);
                break;
            case "bool":
                if($this->value == "1")
                    $val = "Да";
                else if($this->value == "0")
                    $val = "Нет";
                break;
        }
        return $val;
    }
    /**
     * Gets query for [[Feature]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFeature()
    {
        return $this->hasOne(Feature::class, ['id' => 'feature_id']);
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::class, ['id' => 'item_id']);
    }
}
