<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dictinary_item".
 *
 * @property int $id
 * @property string|null $value
 * @property int $sort_id
 * @property int $dictinary_id
 *
 * @property Dictinary $dictinary
 * @property Dictinary $linkedDictinary
 * @property Repairs[] $repairs
 */
class DictinaryItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dictinary_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'dictinary_id'], 'required'],
            [['id', 'sort_id', 'dictinary_id'], 'integer'],
            [['value'], 'safe'],
            [['id'], 'unique'],
            [['dictinary_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dictinary::class, 'targetAttribute' => ['dictinary_id' => 'id']],
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
            'dictinary_id' => 'Dictinary ID',
        ];
    }

    /**
     * Gets query for [[Dictinary]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDictinary()
    {
        return $this->hasOne(Dictinary::class, ['id' => 'dictinary_id']);
    }

    /**
     * Gets query for [[LinkedDictinary]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinkedDictinary()
    {
        return $this->hasOne(Dictinary::class, ['id' => 'linked_dictinary_id']);
    }

    /**
     * Gets query for [[Repairs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRepairs()
    {
        return $this->hasMany(Repairs::class, ['dictinary_item_id' => 'id']);
    }
}
