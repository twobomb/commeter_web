<?php

namespace app\controllers;

use app\models\Category;
use app\models\Department;
use app\models\Employee;
use app\models\search\DepartmentSearch;
use app\models\User;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * DepartmentController implements the CRUD actions for Department model.
 */
class DepartmentController extends AppController
{


    public function actionGetJsonCollection($id){
        if(!$id)return;
        $data = [];
        $allowsDeps = User::isAdmin()? ArrayHelper::getColumn(Department::find()->all(),"id"): Yii::$app->user->identity->getAccessDepartments();

        if($id == "#") {
            $cats = Department::find()->where(["parent_id" => null])->orderBy(["sort_id"=>SORT_ASC])->all();
            foreach ($cats as  $cat) {
                $type = "allow";
                if(!in_array($cat->id,$allowsDeps)){
                    if(!empty(array_intersect(ArrayHelper::getColumn($cat->getAllChildrens(),"id"), $allowsDeps)))
                        $type = "notallow";
                    else
                        continue;
                }

                array_push($data, [
                    "id" => $cat->id,
                    "text" => $cat->name,
                    "children" => Department::findOne(["parent_id" => $cat->id]) ? true : false,
                    'type' => 'print',
                    'data'=>["allow"=>$type],
                    "position" => $cat->sort_id
                ]);
            }
            $data = ["id" => "-1", "text" => "МЧС России по ЛНР", "children" => $data, 'type' => 'root','allow'=>'notallow'];
        }
        else{
            $cats = Department::find()->where(["parent_id" => $id])->orderBy(["sort_id"=>SORT_ASC])->all();
            foreach ($cats as $cat) {
                $type = "allow";
                if(!in_array($cat->id,$allowsDeps)){
                    if(!empty(array_intersect(ArrayHelper::getColumn($cat->getAllChildrens(),"id"), $allowsDeps)))
                        $type = "notallow";
                    else
                        continue;
                }
                array_push($data, [
                    "id" => $cat->id,
                    "text" => $cat->name,
                    "children" => Department::findOne(["parent_id" => $cat->id]) ? true : false,
                    'type' => 'print',
                    'data'=>["allow"=>$type],
                    "position" => $cat->sort_id
                ]);
            }
        }
        header('Content-Type: application/json');

        echo Json::encode($data);
        exit;
    }
    public function actionDelete($id){
        if($id) {
            $department = Department::findOne(["id"=>$id]);
            $childs = Department::find()->where(["parent_id"=>$id])->all();
            if(count($childs) > 0 ){
                return $this->render("/site/error-no-escape",[
                    "title"=>"Ошибка",
                    "message"=>"<i class='fa fa-exclamation-square'></i> Нельзя удалять подразделение у которого во вложениях есть другие подразделения! Сначала удалите или переместите вложенные подразделения! "
                ]);
            }
            if(count($department->items) > 0){
                return $this->render("/site/error-no-escape",[
                    "title"=>"Ошибка",
                    "message"=>"<i class='fa fa-exclamation-square'></i> Нельзя удалять подразделение к которому привязаны средства! Сначала удалить или переместите средства в другое подразделение. Нажмите <a href='/item/search?ItemSearch[department_id]=$department->id'>сюда</a> чтобы посмореть список средств"
                ]);
            }
            if(count($department->employees) > 0){
                return $this->render("/site/error-no-escape",[
                    "title"=>"Ошибка",
                    "message"=>"<i class='fa fa-exclamation-square'></i> Нельзя удалять подразделение к которому сотрудники. Сначала удалите или переместите сотрудников в другое подразделение!"
                ]);
            }
            if($department->delete()){
                showMessage("Успешно удалено");
                return returnBack();
            }else{
                showError("Ошибка удаления обратитесь к администратору");
                return returnBack();
            }

        }
    }

    public function actionGetEmployees($id){
        if($dep = Department::findOne(["id"=>$id])){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ArrayHelper::map($dep->employees,"id","fullName");
        }
		if($id == ""){
            Yii::$app->response->format = Response::FORMAT_JSON;			
			$empls= Employee::find()->where(["in","department_id",\Yii::$app->user->identity->getAccessDepartments()])->all();
			return ArrayHelper::map($empls,"id","fullName");
			
		}
        return null;
    }
    public function actionChangeParent($category,$parent,$position){
        $parent = intval($parent);
        if(!$cat = Department::findOne($category))
            throw  new ServerErrorHttpException("Категория не найдена");
        if((!$par = Department::findOne($parent)) && $parent!= -1)
            throw  new ServerErrorHttpException("Категория не найдена");
        $cat->parent_id = ($parent == -1?null:$par->id);
        $cat->save();


        if($position == 0)
            $newPos = 0;
        else{
            $newPos = Department::find()->where(["parent_id"=>$cat->parent_id])
                ->andWhere(["<>","id",$cat->id])->orderBy(["sort_id"=>SORT_ASC])->all()[$position-1]->sort_id;
            $newPos +=1;
        }
        $cat->sort_id = $newPos;
        $cat->save();

        $sort = Department::find()->where(["parent_id"=>$cat->parent_id])
            ->andWhere(["<>","id",$cat->id])
            ->andWhere([">=","sort_id",$newPos])->orderBy(["sort_id"=>SORT_ASC])->all();
        foreach ($sort  as $s){
            $s->sort_id = ++$newPos;
            $s->save();
        }

    }
    public function actionMarkChildren($id,$is_department){
        $dep = Department::findOne(["id"=>$id]);
        if($dep == null)
            throw new NotFoundHttpException("Подразделение не найдено");

        foreach ($dep->allChildrens as $dep) {
            $dep->is_department = $is_department;
            $dep->save();
        }
        return returnBack();
    }
    public function actionSetDep($id,$val){
        $dep = Department::findOne(["id"=>$id]);
        if($dep == null)
            throw new NotFoundHttpException("Подразделение не найдено");

        $dep->is_department = $val;
        if($dep->save())
            return "OK";
        return "ERROR";
    }
    public function actionGetInfo($id){
        $dep = Department::findOne(["id"=>$id]);
        if($dep == null)
            throw new NotFoundHttpException("Подразделение не найдено");

        Yii::$app->response->format = Response::FORMAT_JSON;

        $dep = [
            "name"=>$dep->name,
            "id"=>$dep->id,
            "is_department"=>$dep->is_department,
            "parent_id"=>$dep->parent_id
        ];
        return $dep;
    }
    public function actionRename($id,$name){
        $cat = Department::findOne($id);
        if(!$cat)return;
        $cat->name = $name;
        $cat->save();
        return Json::encode([
            "id"=>$cat->id,
            "name"=>$cat->name
        ]);
    }
    public function actionAdd($id,$name,$position){
        $cat = new Department();
        $cat->name = $name;
        $cat->sort_id = $position;
        if($id != -1)
            $cat->parent_id = $id;
        if(!$cat->save())
            throw new ServerErrorHttpException();
        return Json::encode([
            "id"=>$cat->id,
            "name"=>$cat->name
        ]);
    }
    public function actionIndex(){
        $cats = Department::find()->all();

        return $this->render("index",[
            "title"=>"Подразделения",
            "catJson"=>Json::encode($cats)
        ]);
    }
}
