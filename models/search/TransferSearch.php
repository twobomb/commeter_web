<?php

namespace app\models\search;

use app\models\Department;
use app\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Transfer;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * TransferSearch represents the model behind the search form of `app\models\Transfer`.
 */
class TransferSearch extends Transfer
{
    public $category_id = null;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'department_id_from', 'department_id_to'], 'integer'],
            [['date', 'description', 'workplace_from', 'workplace_to', 'date_change','category_id','type'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Transfer::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['date_change' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'transfer.id' => $this->id,
            'date' => $this->date,
            'type' => $this->type,
            'department_id_from' => $this->department_id_from,
            'department_id_to' => $this->department_id_to,
            'date_change' => $this->date_change,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'workplace_from', $this->workplace_from])
            ->andFilterWhere(['like', 'workplace_to', $this->workplace_to]);

        if(!User::isAdmin()){
            $dep_ids =  \Yii::$app->user->identity->getAccessDepartments();
            $sdeps = implode(",",$dep_ids);
            $query->having("( department_id_from IN ($sdeps) OR department_id_to IN ($sdeps) )");
        }

        return $dataProvider;
    }
}
