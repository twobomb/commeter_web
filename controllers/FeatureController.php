<?php

namespace app\controllers;

use app\models\Category;
use app\models\Dictinary;
use app\models\Feature;
use app\models\search\FeatureSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FeatureController implements the CRUD actions for Feature model.
 */
class FeatureController extends AppController
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

    /**
     * Lists all Feature models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FeatureSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Feature model.
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
     * Creates a new Feature model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Feature();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                $cats = [];
                if(isset($_POST["pinned_categories"]) && is_array($_POST["pinned_categories"]))
                    $cats = $_POST["pinned_categories"];
                foreach (Category::find()->where(["in","id",$cats])->all() as $c)
                    $model->link("categories",$c);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionDetailFeature(){

        if (isset($_POST['expandRowKey'])) {
            $d = Feature::findOne(["id"=>$_POST['expandRowKey']]);
            ob_start();
            echo "<ul class='list-group'>";
            foreach ($d->categories as $di){
                echo "<li class='list-group-item'>$di->name </li>";
            }
            echo "</ul>";
            $c = ob_get_contents();
            ob_clean();
            return $c;
            //$model = \common\models\Book::findOne($_POST['expandRowKey']);
            //return $this->renderPartial('_book-details', ['model'=>$model]);
        } else {
            return '<div class="alert alert-danger">Не найдено</div>';
        }
    }
    /**
     * Updates an existing Feature model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {

            $cats = [];
            if(isset($_POST["pinned_categories"]) && is_array($_POST["pinned_categories"]))
                $cats = $_POST["pinned_categories"];
            $toDelete = [];
            foreach ($model->categories as $c){
                if(!in_array($c->id,$cats))
                    array_push($toDelete,$c);
            }
            foreach ($toDelete as $del)
                $model->unlink("categories",$del,true);

            $toAdd = [];
            $_ccats = ArrayHelper::getColumn($model->categories,"id");
            foreach ($cats as $c){
                if(!in_array($c,$_ccats))
                    array_push($toAdd,Category::findOne(["id"=>$c]));
            }
            foreach ($toAdd as $add)
                $model->link("categories",$add);


            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Feature model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id){
        $m = $this->findModel($id);
        $confirm = false;
        if(isset($_POST["confirm"]) && $_POST["confirm"] == "1")
            $confirm = true;

        if( count($m->categories) >0 && !$confirm){
            return $this->render("confirm",[
                "model"=>$m
            ]);
        }

        $m->unlinkAll("featureValues",true);
        $m->unlinkAll("categories",true);
        $m->unlinkAll("fields",true);

        if($m->delete()){
            showMessage("Свойство '$m->name' успешно удалено");
        }
        else{
            showError("Ошибка удаления свойства '$m->name' ");
        }

        return returnBack();
    }

    /**
     * Finds the Feature model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Feature the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Feature::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
