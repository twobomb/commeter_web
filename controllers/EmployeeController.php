<?php

namespace app\controllers;

use app\models\Employee;
use app\models\search\EmployeeSearch;
use Yii;
use yii\bootstrap5\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends AppController
{
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

    public function actionGetInfo($id){

        $em = Employee::findOne(["id"=>$id]);
        if($em == null)
            throw new NotFoundHttpException("Сотрудник не найден");

        \Yii::$app->response->format =  Response::FORMAT_JSON;

        return [
          "department_id"=>$em->department_id,
          "fullName"=>$em->getFullName(),
          "first_name"=>$em->first_name,
          "last_name"=>$em->last_name,
          "second_name"=>$em->second_name,
          "id"=>$em->id,
          "post"=>$em->post,
          "cabinet"=>$em->cabinet,
        ];


    }
    public function actionDetailEmployee(){

    if (isset($_POST['expandRowKey'])) {
        $emp = Employee::findOne(["id"=>$_POST['expandRowKey']]);

        ob_start();
         $items = $emp->items;?>

            <div class="card cardMyList">
                <div class="card-header">
                    <h6>Список сресдтв закрепленных за сотрудником [<?=count($items)?>]: </h6>
                </div>
              <?PHP

            echo "<div class=\"card-body\"><ol>";

            if(count($items) > 0):
                foreach ($items as $it):
                ?>
                <li><?= Html::a($it->name." ($it->inv_num)","/item/view?id=$it->id",["class"=>"btn btn-outline-primary mt-1"]) ?></li>
                <?PHP
                endforeach;
            else:
                echo "Список пуст";
            endif;

            echo "</ol></div>";
            ?>
        </div>
    <?php
        $res = ob_get_contents();
        ob_clean();
        return $res;
    } else {
        return '<div class="alert alert-danger">Не найдено</div>';
    }
}
    /**
     * Lists all Employee models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employee model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id){
		
		$model = $this->findModel($id);
        $canAccess = true;
        if(!in_array($model->department_id,ArrayHelper::getColumn(\Yii::$app->user->identity->getAccessDepartments(false), 'id')))
            $canAccess = false;

        foreach (ArrayHelper::getColumn($model->advancedDepartments,"id") as $dep_id){
            if(in_array($dep_id,ArrayHelper::getColumn(\Yii::$app->user->identity->getAccessDepartments(false), 'id'))) {
                $canAccess = true;
                break;
            }
        }
        if(!$canAccess)
			throw new ForbiddenHttpException("У вас нет доступа к департаменту сотрудника!");
		
		
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Employee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Employee();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Employee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id){

        $model = $this->findModel($id);
		
		if(!in_array($model->department_id,ArrayHelper::getColumn(\Yii::$app->user->identity->getAccessDepartments(false), 'id')))
			throw new ForbiddenHttpException("У вас нет доступа к департаменту сотрудника!");
		
		
        if ($this->request->isPost && $model->load($this->request->post())){
            if(!$model->is_responsible && count($model->itemsResponsible) > 0 )
                $model->addError("is_responsible","К сотруднику привязаны средства как к мат.ответственному. Сначала поменяйте мат.ответственного!");
            if($model->validate(null,false) && $model->save())
                return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    public function actionReplaceResponsible($id){
        $model = $this->findModel($id);
        if(!in_array($model->department_id,ArrayHelper::getColumn(\Yii::$app->user->identity->getAccessDepartments(false), 'id')))
            throw new ForbiddenHttpException("У вас нет доступа к департаменту сотрудника!");
        if(\Yii::$app->request->isPost){
            $m1 = Employee::findOne(["id"=>$_POST["old_resp_id"]]);
            $m2 = Employee::findOne(["id"=>$_POST["new_resp_id"]]);
            if(!$m1 || !$m2)
                showError("Нужно выбрать сотрудников!");
            else if($m1->id == $m2->id)
                showError("Нельзя выбирать одного и того же!");
            else{
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();
                try {
                    foreach ($m1->itemsResponsible as $it) {
                            $stateBefore = GetItemFullState($it);
                            $it->ignoreAllow = true;
                            $it->responsible_employee_id = $m2->id;
                            if (!$it->save())
                                throw new ForbiddenHttpException($it->errors);
                            saveHistory($it, $stateBefore, "change");
                        }
                        $transaction->commit();
                        showMessage("Сохранено");

                        return $this->redirect("/item/index");
                } catch(\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }
        }
        return $this->render('replace-responsible', [
            'model' => $model,
        ]);
    }
    /**
     * Deletes an existing Employee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {

        $model = $this->findModel($id);
		
		
		if(!in_array($model->department_id,ArrayHelper::getColumn(\Yii::$app->user->identity->getAccessDepartments(false), 'id')))
			throw new ForbiddenHttpException("У вас нет доступа к департаменту сотрудника!");
		
        if(count($model->items) > 0 )
            showError("К сотруднику привязаны средства, сначала отвяжите их!");
        else if(count($model->itemsResponsible) > 0)
            showError("Сотрудник является мат.ответственным для некоторых средств, сначала поменяйте мат. ответственного!");
        else
            $model->delete();

        return returnBack();
    }

    /**
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employee::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
