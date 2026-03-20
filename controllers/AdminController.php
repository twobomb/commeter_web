<?php

namespace app\controllers;

use app\models\search\UserSearch;
use app\models\Statuses;
use app\models\User;
use Yii;
use yii\base\BaseObject;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AdminController extends AppController
{



    public function actionHistory(){

        return $this->render('history', [
        ]);
    }
    public function actionCreateUser(){
        $model = new \app\models\User();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->pwd_hash = Yii::$app->getSecurity()->generatePasswordHash($model->password);

                if($model->save()){
                    showMessage('success', "Пользователь создан!");
                    return $this->redirect("/admin/update-user?id=$model->id");
                }
                showError('error', "Ошибка сохранения!");
            }
        }

        return $this->render('users/create_user', [
            'model' => $model,
        ]);
    }


    public function actionUsersList()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('users/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionUpdateUser($id){
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_UPDATE;

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            if(!empty($this->request->post("User")["password"])){
                $model->scenario = User::SCENARIO_DEFAULT;
                $model->password = $this->request->post("User")["password"];
                if($model->validate()){
                    $model->pwd_hash = Yii::$app->getSecurity()->generatePasswordHash($model->password);
                    $model->save();
                }
                else
                    return $this->render('users/update', [
                        'model' => $model
                    ]);
            }
            return $this->redirect(['/admin/users-list', 'id' => $model->id]);
        }

        return $this->render('users/update', [
            'model' => $model,
        ]);
    }


    public function actionSetStatus($id,$status)
    {
        $t = Trables::find()->where(["id"=>$id])->one();
        $s = Statuses::find()->where(["id"=>$status])->one();
        if($t && $s){
            $oldS = $t->status->name;
            $newS = $s->name;
            $t->status_id = $s->id;
            if($t->save()){
                $ts = new TrableSolutionHistory();
                $ts->description  = "'".Yii::$app->user->identity->name."' сменил статус с '$oldS' на '$newS'";
                $ts->type  = "system";
                $ts->trable_id = $t->id;
                $ts->user_id  =  Yii::$app->user->id;
                $ts->save();
            }
            return "OK";
        }
        return "";
    }
    public function actionBlockUser($id,$val)    {
        $user = User::findOne(["id"=>$id]);
        if($user->id == Yii::$app->user->id){
            showError("Нельзя заблокировать самого себя");
            return $this->redirect(Yii::$app->request->referrer);
        }
        if($user && in_array($val,["1","0"])){
            $user->block = $val;
            if(!$user->validate())
                throw  new Exception(var_dump_ret($user->errors));
            $user->save();
        }
        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
