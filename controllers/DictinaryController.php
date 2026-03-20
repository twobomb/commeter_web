<?php

namespace app\controllers;

use app\models\Dictinary;
use app\models\DictinaryItem;
use app\models\DictinarySearch;
use app\models\Employee;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DictinaryController implements the CRUD actions for Dictinary model.
 */
class DictinaryController extends AppController
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
    public function actionDetailDictinary(){

        if (isset($_POST['expandRowKey'])) {
            $d = Dictinary::findOne(["id"=>$_POST['expandRowKey']]);
            echo "<ul class='list-group'>";
            foreach ($d->dictinaryItems as $di){
                echo "<li class='list-group-item'>$di->value </li>";
            }
            echo "</ul>";

            //$model = \common\models\Book::findOne($_POST['expandRowKey']);
            //return $this->renderPartial('_book-details', ['model'=>$model]);
        } else {
            return '<div class="alert alert-danger">Не найдено</div>';
        }
    }

    /**
     * Lists all Dictinary models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DictinarySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Dictinary model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Dictinary model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Dictinary();

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            $dis =  [];
            if(isset($_POST["DictinaryItem"]) &&  is_array($_POST["DictinaryItem"]))
                $dis = $_POST["DictinaryItem"];
            $deleteItems = DictinaryItem::find()->where(["dictinary_id"=>$model->id])->andWhere(["not in","id",array_keys($dis)])->all();
            foreach ($deleteItems as $di) {
                if(!$di->delete())
                    showMessage("Удаление '$di->value' не удалось!","danger","Ошибка");
            }


            foreach (DictinaryItem::find()->where(["in","id",array_keys($dis)])->all() as $di){
                $di->sort_id = $dis[$di->id];
                $di->save();
            }

            $ndis =  [];
            if(isset($_POST["NewDictinaryItem"]) && is_array($_POST["NewDictinaryItem"]))
                $ndis = $_POST["NewDictinaryItem"];

            foreach ($ndis as $value=>$sort_id){
                $di = new DictinaryItem();
                $di ->sort_id = $sort_id;
                $di->value = $value;
                $di->dictinary_id = $model->id;
                if(!$di->save())
                    showMessage("Добавление '$di->value' не удалось!", "danger", "Ошибка");
            }


            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Dictinary model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            $dis =  [];
            if(isset($_POST["DictinaryItem"]) &&  is_array($_POST["DictinaryItem"]))
                $dis = $_POST["DictinaryItem"];
            $deleteItems = DictinaryItem::find()->where(["dictinary_id"=>$model->id])->andWhere(["not in","id",array_keys($dis)])->all();
            foreach ($deleteItems as $di) {
                if(!$di->delete())
                    showMessage("Удаление '$di->value' не удалось!","danger","Ошибка");
            }


            foreach (DictinaryItem::find()->where(["in","id",array_keys($dis)])->all() as $di){
                $di->sort_id = $dis[$di->id];
                $di->save();
            }

            $ndis =  [];
            if(isset($_POST["NewDictinaryItem"]) && is_array($_POST["NewDictinaryItem"]))
                $ndis = $_POST["NewDictinaryItem"];

            foreach ($ndis as $value=>$sort_id){
                $di = new DictinaryItem();
                $di ->sort_id = $sort_id;
                $di->value = $value;
                $di->dictinary_id = $model->id;
                if(!$di->save())
                    showMessage("Добавление '$di->value' не удалось!", "danger", "Ошибка");
            }


            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Dictinary model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return returnBack();
    }

    /**
     * Finds the Dictinary model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Dictinary the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Dictinary::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
