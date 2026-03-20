<?php

namespace app\models\search;

use app\models\Item;
use app\models\Repair;
use app\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * TransferSearch represents the model behind the search form of `app\models\Transfer`.
 */
class RepairSearch extends Repair
{
    public $category_id = null;
    public $inv_num = null;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'description', 'sum', 'item_id', 'type','date_change','category_id','inv_num','id'], 'safe'],
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
        $query = Repair::find();

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
            'repairs.id' => $this->id,
            'type' => $this->type,
            'item_id' => $this->item_id,
            'date' => $this->date,
            'date_change' => $this->date_change,
        ]);
        $query->andFilterWhere(['like', 'description', $this->description]);

        $query->joinWith(['item' => function (ActiveQuery $query) {
            return $query
                ->andFilterWhere(["item.category_id"=>$this->category_id])
            ->andFilterWhere(["LIKE","item.inv_num",$this->inv_num]);
            }]);

        if(!User::isAdmin()){
            $dep_ids =  \Yii::$app->user->identity->getAccessDepartments();
            $itemsids = Item::find()->where(["in","department_id",$dep_ids])->select("id")->column();
            $query->having(["in","item_id",$itemsids]);
        }

        return $dataProvider;
    }
}
