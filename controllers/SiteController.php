<?php

namespace app\controllers;

use app\models\Category;
use app\models\Department;
use app\models\DictinaryItem;
use app\models\Employee;
use app\models\Feature;
use app\models\FeatureValue;
use app\models\Item;
use app\models\LoginForm;
use app\models\Statuses;
use app\models\Trables;
use app\models\search\TrablesSearch;
use app\models\TrableSolutionHistory;
use app\models\User;
use Yii;
use yii\base\BaseObject;
use yii\db\Connection;
use yii\db\Query;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * SiteController implements the CRUD actions for Trables model.
 */
class SiteController extends AppController
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }
	public function actionExcelPrepare(){
        if(!User::isAdmin())
            throw new ForbiddenHttpException("Запрещено");

        if(Yii::$app->request->isPost && isset($_POST["name"])){
            $dep=  Department::findOne(["id"=>$_POST["department_id"]]);
            if(!$dep->canAccess())
                throw new ForbiddenHttpException("Доступ к подразделению запрещен!");
            $resp = Employee::findOne(["id"=>$_POST["responsible_employee_id"]]);
            if(!$resp || !$resp->is_responsible)
                throw new ForbiddenHttpException("Не найден мат. отвественый");


            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            $added = 0;
            try {
                foreach ($_POST["name"] as $i=>$name){

                    $inv_num = $_POST["inv"][$i];
                    $sum = floatval($_POST["sum"][$i]);
                    $work = $_POST["work"][$i];
                    $count= intval($_POST["count"][$i]);
                    $cat= Category::findOne(["id"=>$_POST["category"][$i]]);
                    if(!$cat)
                        throw new ForbiddenHttpException("Категория не найдена!");
                    if($count < 0 )
                        throw new ForbiddenHttpException("Количеество меньше нуля!");
                    if($count > 1000 )
                        throw new ForbiddenHttpException("Слишком много количества!");
                    for ($j = 0; $j < $count;$j++){

                        $item = new Item();
                        $item->inv_num = $inv_num;
                        $item->workspace = $work;
                        $item->category_id = $cat->id;
                        $item->responsible_employee_id = $resp->id;
                        $item->department_id = $dep->id;
                        $item->name = $name;
                        if(!$item->save()) {
                            throw new ServerErrorHttpException("Ошибка сохранения");
                        }
                        $added++;
                        foreach ($item->category->features as $f){
                            if($f->name == "Стоимость, руб." && !empty($sum)){
                                $fv = new FeatureValue();
                                $fv->feature_id = $f->id;
                                $fv->value = $sum;
                                $item->link('featureValues',$fv);
                            }
                            if($f->name == "Текущее состояние" ){
                                $fv = new FeatureValue();
                                $fv->feature_id = $f->id;
                                $fv->value = DictinaryItem::findOne(["value"=>"В эксплуатации"])->id;
                                $item->link('featureValues',$fv);
                            }
                        }
                    }

                }

                $transaction->commit();
                showMessage("Добавлено средств: $added ");
                return $this->redirect("/item/index");

            } catch(\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        return $this->render('excel-prepare', [
        ]);
	}

    /**
     * Lists all Trables models.
     *
     * @return string
     */
    public function actionError(){
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null)
            return $this->render('error', ['exception' => $exception]);
    }
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogout()
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(["/"]);
        \Yii::$app->user->logout(true);
        return $this->redirect(["/"]);
    }
    public function actionLogin()    {
        if (!Yii::$app->user->isGuest)
            return $this->redirect(["site/index"]);

        $request = Yii::$app->request->post();
        $user = new LoginForm();
        if($request)
        {
            if ($user->load($request) && $user->login()){
                return $this->redirect(["/"]);
            }

            $session = Yii::$app->session;
        }


        $user->password = '';
        return $this->render('login', [
            'model' => $user,
        ]);
    }

 /*   public function actionGenerateHistory(){
        die;
        foreach (Item::find()->all() as $item){
            saveHistory($item,[]);
        }
        echo "COMPLETE";
    }*/
    public function actionMigrate(){
        //МИГРАЦИЯ СО СТАРОЙ БД
        echo Yii::$app->getSecurity()->generatePasswordHash("123456");
        die;
        $db_old = new Connection( [
            'dsn' => 'mysql:host=localhost;dbname=commeter',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8'
        ]);
        //Правила пасинга стараятаблица => новая таблица, _default умолчанию поле в новой
        $mirgateConfig = [
          "users"=>[
              "login"=>"login",
              "username"=>"name",
              "_defaults"=>[
                  "role"=>"user",
                  "pwd_hash"=> Yii::$app->getSecurity()->generatePasswordHash("Qwerty321!"),
                  "block"=>"0"
              ]
          ],
            "dictinary"=>[
                "id"=>"id",
                "name"=>"name"
            ],
            "dictinary_item"=>[
                "id"=>"id",
                "dictinary_id"=>"dictinary_id",
                "value"=>"value",
            ],
            "department"=>[
                "id"=>"id",
                "is_deleted"=>"is_deleted",
                "name"=>"name",
                "parent_id"=>"parent_id",
                "sort_id"=>"sort_id",
            ],
            "category"=>[
                "id"=>"id",
                "is_can_add"=>"is_can_add",
                "name"=>"name",
                "parent_id"=>"parent_id",
                "sort_id"=>"sort_id",
                "is_deleted"=>"is_deleted",
                "is_hidden"=>"is_hidden",
            ],
            "employee"=>[
                "id"=>"id",
                "department_id"=>"department_id",
                "first_name"=>"first_name",
                "second_name"=>"second_name",
                "last_name"=>"last_name",
                "is_deleted"=>"is_deleted",
                "post"=>"post",
                "sort_id"=>"sort_id",
                "cabinet"=>"cabinet",
            ],
            "feature"=>[
                "id"=>"id",
                "dictinary_id"=>"dictinary_id",
                "is_required"=>"is_required",
                "name"=>"name",
                "type"=>"type",
                "sort_id"=>"sort_id",
            ],

            "featurecategories"=>[
                "Category_id"=>"Category_id",
                "Feature_id"=>"Feature_id",
            ],
            "item"=>[
                "category_id"=>"category_id",
                "date_change"=>"date_change",
                "department_id"=>"department_id",
                "employee_id"=>"employee_id",
                "id"=>"id",
                "inv_num"=>"inv_num",
                "name"=>"name",
                "workspace"=>"workspace",
                "_defaults"=>[
                    'responsible_employee_id'=>273
                    ]

            ],
            "feature_values"=>[
                "id"=>"id",
                "feature_id"=>"feature_id",
                "item_id"=>"item_id",
                "value"=>"value",
            ]
        ];


        $inserts = [];

        ///правила замены ID
        $idRepalcers = [
            "item"=>[
                "key"=>"id",
                "data"=>[],
                "iterator"=>1,
                "relations"=>[
                    "feature_values"=>"item_id"
                ]
            ],
            "department"=>[
                "key"=>"id",
                "data"=>[],
                "iterator"=>1,
                "relations"=>[
                    "employee"=>"department_id",
                    "item"=>"department_id",
                    "department"=>"parent_id",
                ]
            ],
        ];

        foreach ($mirgateConfig as $table=>$cfg){

            $inserts[$table] = [];
            $q = new Query();
            $res = $q->from($table)->all($db_old);
            if($table == "department"){
                $res = sortByHierarchy($res,null);
            }


            foreach ($res as $obj){
                $vals = [];
                foreach ($cfg as $oldcol=>$newcol) {
                    if($oldcol == "_defaults")
                        $vals = array_merge($vals,$newcol);
                    else {

                        if(in_array($table,array_keys($idRepalcers)) && $newcol == $idRepalcers[$table]["key"]){
                                $idRepalcers[$table]["data"][$obj[$oldcol]] =$idRepalcers[$table]["iterator"]++;
                                $vals[$newcol] =$idRepalcers[$table]["data"][$obj[$oldcol]];
                        }else{
                            $isReplaced = false;
                            foreach ($idRepalcers as $tbl=>$d){
                                if(in_array($table,array_keys($d["relations"]))){
                                    if($d["relations"][$table] == $newcol){
                                        if($obj[$oldcol] != null){
                                            $vals[$newcol] = $d["data"][$obj[$oldcol]];
                                            $isReplaced = true;
                                        }
                                    }
                                }
                            }
                            if(!$isReplaced)
                                $vals[$newcol] = $obj[$oldcol];
                        }




                    }
                }
                foreach ($vals as $k=>$v) {
                    if($v == null)
                        $vals[$k] = "NULL";
                    else
                        $vals[$k] = "'$v'";
                }

                $insert = "INSERT INTO $table (".implode(",",array_keys($vals)).") VALUES (".implode(",",$vals).");";
                array_push($inserts[$table],$insert);
            }
        }

        $sql = "";
        echo "<pre>";
        foreach ($inserts as $cat=>$arrSqls){
            $sql.= implode("\n",$arrSqls);
        }
        echo $sql;
        die;
    }

}
