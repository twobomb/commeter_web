<?php

namespace app\models\search;

use app\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Employee;
use yii\db\Expression;

/**
 * EmployeeSearch represents the model behind the search form of `app\models\Employee`.
 */
class EmployeeSearch extends Employee
{

    public $fio;
    /**
     *
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_deleted', 'sort_id', 'department_id'], 'integer'],
            [['first_name', 'second_name', 'last_name', 'post', 'cabinet','fio'], 'safe'],
            [['is_responsible'], 'boolean'],
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
        $query = Employee::find();
        //$query->addSelect(new Expression("CONCAT(second_name, ' ',first_name , ' ', last_name) AS fio"));
        //$query->select(["CONCAT(second_name, ' ',first_name , ' ', last_name) AS fio","post"]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'is_deleted' => $this->is_deleted,
            'is_responsible' => $this->is_responsible,
            'sort_id' => $this->sort_id,
            'department_id' => $this->department_id,
        ]);
        $query->andFilterWhere(['like', "CONCAT(second_name, ' ',first_name , ' ', last_name)", $this->fio])
            ->andFilterWhere(['like', 'post', $this->post])
            ->andFilterWhere(['like', 'cabinet', $this->cabinet]);

        $query->having(["in","department_id",\Yii::$app->user->identity->getAccessDepartments()]);

        return $dataProvider;
    }
}
