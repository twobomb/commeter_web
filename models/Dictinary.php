<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "dictinary".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property DictinaryItem[] $dictinaryItems
 * @property DictinaryItem[] $dictinaryItems0
 * @property Feature[] $features
 */
class Dictinary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dictinary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
        ];
    }


    /**
     * Gets query for [[DictinaryItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDictinaryItems()
    {
        $items =  \app\models\DictinaryItem::find()->where(["dictinary_id"=>$this->id])->orderBy(["sort_id"=>SORT_ASC])->all();
        return $items;
    }

    /**
     * Gets query for [[DictinaryItems0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDictinaryItems0()
    {
        return $this->hasMany(DictinaryItem::class, ['linked_dictinary_id' => 'id']);
    }

    /**
     * Gets query for [[Features]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFeatures()
    {
        return $this->hasMany(Feature::class, ['dictinary_id' => 'id']);
    }
}
