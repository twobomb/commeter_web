<?php

namespace app\controllers;

use app\models\Employee;
use app\models\Feature;
use app\models\Tag;
use app\models\search\TagSearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TagController implements the CRUD actions for Tag model.
 */
class TagController extends AppController
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



    public function actionDetailTag(){

        if (isset($_POST['expandRowKey'])) {
            $d = Tag::findOne(["id"=>$_POST['expandRowKey']]);
            ob_start();
            echo "<ul class='list-group'>";
            if(count($d->items) == 0)
                echo "<li class='list-group-item'>Пока не привязано ни одного средства <i style='font-size: 20px' class='fa fa-frown'></i></li>";
            foreach ($d->items as $di){
                echo "<li class='list-group-item'><a href='/item/view?id=$di->id'>[{$di->category->name}] $di->name ($di->inv_num) </a> </li>";
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
     * Lists all Tag models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TagSearch();
        $searchModel->user_id = \Yii::$app->user->id;
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tag model.
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
     * Creates a new Tag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Tag();
        $model->user_id = \Yii::$app->user->identity->getId();

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
     * Updates an existing Tag model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tag model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Tag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Tag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tag::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
