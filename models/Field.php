<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "field".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $feature_id
 * @property int $column_excel_width
 * @property int $column_valign
 * @property int $column_halign
 * @property string|null $type
 *
 * @property Expression[] $expressions
 * @property Feature $feature
 * @property FieldViews[] $fieldViews
 * @property Requests[] $requests
 */
class Field extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'string'],
            [['feature_id', 'column_excel_width', 'column_valign', 'column_halign'], 'integer'],
            [['column_excel_width', 'column_valign', 'column_halign'], 'required'],
            [['feature_id'], 'exist', 'skipOnError' => true, 'targetClass' => Feature::class, 'targetAttribute' => ['feature_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'feature_id' => 'Feature ID',
            'column_excel_width' => 'Column Excel Width',
            'column_valign' => 'Column Valign',
            'column_halign' => 'Column Halign',
            'type' => 'Type',
        ];
    }

    /**
     * Gets query for [[Expressions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpressions()
    {
        return $this->hasMany(Expression::class, ['field_id' => 'id']);
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
     * Gets query for [[FieldViews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFieldViews()
    {
        return $this->hasMany(FieldViews::class, ['field_id' => 'id']);
    }

    /**
     * Gets query for [[Requests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequests()
    {
        return $this->hasMany(Requests::class, ['order_by_field_id' => 'id']);
    }
}
