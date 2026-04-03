<?php

namespace app\controllers;

use app\models\User;
use yii\helpers\Console;
use yii\web\ForbiddenHttpException;

class AppController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        // 1. Если пользователь гость
        if (\Yii::$app->user->isGuest) {
            // Разрешаем доступ только к странице авторизации (и, возможно, к другим публичным экшенам)
            if (!in_array($action->id,['login','error','help'])) {
                // Редирект на страницу входа
                return $this->redirect(['/site/login'])->send(); // send() гарантирует остановку выполнения, но можно просто вернуть false после redirect()
                // Либо: $this->redirect(['/site/login']); return false;
            }
            // Для гостя на странице логина – пропускаем дальше
            return parent::beforeAction($action);
        }

        // 2. Пользователь авторизован
        try {
            \Yii::$app->user->identity->last_activity = nowdate();
            \Yii::$app->user->identity->save();
        } catch (\Exception $e) {
            // Игнорируем ошибки сохранения
        }

        // Проверка блокировки
        if (\Yii::$app->user->identity->block) {
            \Yii::$app->user->logout(true);
            showError("Вы были заблокированы", "Внимание", 10000);
            return $this->redirect("/")->send(); // или return false после редиректа
        }

        // 3. Если пользователь не администратор – применяем ограничения
        if (!User::isAdmin()) {
            $denyUsers = [
                "admin" => ["*"],
                "category" => ["link-features", "index", "change-parent", "rename", "add", "update", "delete"],
                "feature" => ["index", 'view', "create", "update", "delete"],
                "department" => ["change-parent", "mark-children", "set-dep", "index", "rename", "add", "update", "delete"],
                "dictinary" => ["index", "rename", "add", "update", "delete"],
            ];

            if (isset($denyUsers[$this->id])) {
                $allow = true;
                if (in_array("*", $denyUsers[$this->id])) {
                    $allow = false;
                }
                if (in_array($this->action->id, $denyUsers[$this->id])) {
                    $allow = false;
                }
                if (!$allow) {
                    throw new ForbiddenHttpException('Вам запрещен доступ на эту страницу');
                }
            }
        }

        return parent::beforeAction($action);
    }
}