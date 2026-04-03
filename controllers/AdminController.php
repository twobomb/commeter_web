<?php

namespace app\controllers;

use app\models\History;
use app\models\Category;
use app\models\Department;
use app\models\Item;
use app\models\search\UserSearch;
use app\models\Statuses;
use app\models\User;
use Yii;
use yii\base\BaseObject;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\NotFoundHttpException;

class AdminController extends AppController{


    public function actionDepartmentsDashboard()
    {
        $date24 = date('Y-m-d H:i:s', time() - 24 * 3600);
        $date7 = date('Y-m-d H:i:s', time() - 7 * 24 * 3600);
        
        $rows = History::find()
            ->select([
                'department',
                "SUM(CASE WHEN action = 'create' AND date >= '{$date24}' THEN 1 ELSE 0 END) AS create_24",
                "SUM(CASE WHEN action = 'change' AND date >= '{$date24}' THEN 1 ELSE 0 END) AS change_24",
                "SUM(CASE WHEN action = 'delete' AND date >= '{$date24}' THEN 1 ELSE 0 END) AS delete_24",
                "SUM(CASE WHEN action = 'create' AND date >= '{$date7}' THEN 1 ELSE 0 END) AS create_7",
                "SUM(CASE WHEN action = 'change' AND date >= '{$date7}' THEN 1 ELSE 0 END) AS change_7",
                "SUM(CASE WHEN action = 'delete' AND date >= '{$date7}' THEN 1 ELSE 0 END) AS delete_7",
                'MAX(date) AS last_date',
            ])
            ->where(['in', 'action', ['create', 'change', 'delete']])
            ->andWhere(['>=', 'date', $date7])
            ->andWhere(['is not', 'department', null])
            ->andWhere(['<>', 'department', ''])
            ->groupBy('department')
            ->having("(SUM(CASE WHEN action = 'create' AND date >= '{$date7}' THEN 1 ELSE 0 END) > 0) OR (SUM(CASE WHEN action = 'change' AND date >= '{$date7}' THEN 1 ELSE 0 END) > 0) OR (SUM(CASE WHEN action = 'delete' AND date >= '{$date7}' THEN 1 ELSE 0 END) > 0)")
            ->orderBy(['last_date' => SORT_DESC])
            ->asArray()
            ->all();
        return $this->render('departments-dashboard', [
            'rows' => $rows,
            'date24' => $date24,
            'date7' => $date7,
        ]);
    }

    public function actionHistory(){

        return $this->render('history', [
        ]);
    }

   /* public function actionGeneratePasswords()
    {
        // Разрешаем скрипту работать неограниченно долго
        set_time_limit(0);

        // Проверка доступа (по желанию)
        // if (!Yii::$app->user->can('admin')) {
        //     throw new \yii\web\ForbiddenHttpException('Доступ запрещён');
        // }

        // Используем пакетную выборку (например, по 100 пользователей за раз)
        $usersQuery = \app\models\User::find()
            ->where(['!=', 'login', 'admin'])
            ->select(['id', 'name', 'login']); // выбираем только нужные поля

        $generated = [];
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Пакетная обработка: каждый batch - массив из 100 пользователей
            foreach ($usersQuery->each(100) as $user) {
                $plainPassword = Yii::$app->security->generateRandomString(8);
                $user->pwd_hash = Yii::$app->security->generatePasswordHash($plainPassword);
                $user->save(false); // сохраняем без повторной валидации

                // Собираем данные для вывода
                $generated[] = [
                    'name'     => $user->name,
                    'login'    => $user->login,
                    'password' => $plainPassword,
                ];
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->renderContent('<h3>Ошибка: ' . htmlspecialchars($e->getMessage()) . '</h3>');
        }

        // Вывод результатов
        $html = '<h1>Сгенерированные пароли</h1>';
        if (empty($generated)) {
            $html .= '<p>Нет пользователей для обновления паролей.</p>';
        } else {
            $html .= '<table border="1" cellpadding="5">
                    <thead>
                        <tr><th>Имя</th><th>Логин</th><th>Новый пароль</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($generated as $item) {
                $html .= '<tr>
                         <td>' . htmlspecialchars($item['name']) . '</td>
                         <td>' . htmlspecialchars($item['login']) . '</td>
                         <td>' . htmlspecialchars($item['password']) . '</td>
                       </tr>';
            }
            $html .= '</tbody>
                </table>';
        }
        $html .= '<br>' . \yii\helpers\Html::a('Назад', ['index'], ['class' => 'btn btn-primary']);

        return $this->renderContent($html);
    }*/
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
