<?php

namespace app\controllers;

use app\models\Category;
use app\models\Department;
use app\models\Feature;
use app\models\search\CategorySearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends AppController
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
//                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }


    public function actionLinkFeatures(){

        $id = $_POST["id"];
        $feats = isset($_POST["feats"])?$_POST["feats"]:[];
        if(!is_array($feats))
            $feats =[];
        $c = Category::findOne(["id"=>$id]);
        if($c){

            $toDelete = [];
            foreach ($c->features as $f){
                if(!in_array($f->id,$feats))
                    array_push($toDelete,$f);
            }
            foreach ($toDelete as $del)
                $c->unlink("features",$del,true);

            $toAdd = [];
            $_ffeats = ArrayHelper::getColumn($c->features,"id");
            foreach ($feats as $f){
                if(!in_array($f,$_ffeats))
                    array_push($toAdd,Feature::findOne(["id"=>$f]));
            }
            foreach ($toAdd as $add)
                $c->link("features",$add);
            return "OK";
        }
        return "ERROR";

    }
    public function actionGetInfo($id){
        $dep = Category::findOne(["id"=>$id]);
        if($dep == null)
            throw new NotFoundHttpException("category не найдено");

        Yii::$app->response->format = Response::FORMAT_JSON;

        $dep = [
            "is_can_add"=>$dep->is_can_add,
            "id"=>$dep->id,
            "name"=>$dep->name,
            "parent_id"=>$dep->parent_id
        ];
        return $dep;
    }
    public function actionSetCanAdd($id,$val){
        $dep = Category::findOne(["id"=>$id]);
        if($dep == null)
            throw new NotFoundHttpException("category не найдено");

        $dep->is_can_add =$val;
        if($dep->save())
            return "OK";
        return "ERROR";
    }
    public function actionGetFeatures($id){
        if(!$id)return;
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        return ArrayHelper::getColumn(Category::findOne(["id"=>$id])->features,"id");
    }
    public function actionGetJsonCollection($id){
        if(!$id)return;
        $data = [];

        if($id == "#") {
            $cats = Category::find()->where(["parent_id" => null])->orderBy(["sort_id"=>SORT_ASC])->all();

            foreach ($cats as $cat)
                array_push($data, [
                    "id" => $cat->id,
                    "text" => $cat->name,
                    "children"=>Category::findOne(["parent_id"=>$cat->id])?true:false,
                    'type'=>'print',
                    "position"=>$cat->sort_id
                ]);
            $data = ["id" => "-1", "text" => "Средства связи", "children" => $data, 'type' => 'root'];
        }
        else{
            $cats = Category::find()->where(["parent_id" => $id])->orderBy(["sort_id"=>SORT_ASC])->all();
            foreach ($cats as $cat)
                array_push($data, [
                    "id" => $cat->id,
                    "text" => $cat->name,
                    "children"=>Category::findOne(["parent_id"=>$cat->id])?true:false,
                    'type'=>'print',
                    "position"=>$cat->sort_id
                ]);
        }
        header('Content-Type: application/json');

        echo Json::encode($data);
        exit;
    }
        public function actionDelete($id){
            if($id) {
                $model = Category::findOne($id);
                if(count($model->items) > 0 ){
                    return $this->render("/site/error-no-escape",[
                        "title"=>"Ошибка",
                        "message"=>"<i class='fa fa-exclamation-square'></i> Нельзя удалять категории у которых есть привязанные средства! Сначала удалите средства. <a href='/item/search?ItemSearch[category_id]=$model->id'>Нажмите</a> чтобы увидеть список средств категории  "
                    ]);
                }
                $childs = Category::find()->where(["parent_id"=>$model->id])->all();
                if(count($childs) > 0){
                        return $this->render("/site/error-no-escape",[
                            "title"=>"Ошибка",
                            "message"=>"<i class='fa fa-exclamation-square'></i> Нельзя удалять категории у которого во вложениях есть другие категории! Сначала удалите или переместите вложенные категории! "
                        ]);
                    }

                if($model->delete()){
                    showMessage("Успешно удалено");
                    return returnBack();
                }else{
                    showError("Ошибка удаления обратитесь к администратору");
                    return returnBack();
                }
            }
        }


        public function actionChangeParent($category,$parent,$position){
            $parent = intval($parent);
            if(!$cat = Category::findOne($category))
                throw  new ServerErrorHttpException("Категория не найдена");
            if((!$par = Category::findOne($parent)) && $parent!= -1)
                throw  new ServerErrorHttpException("Категория не найдена");
            $cat->parent_id = ($parent == -1?null:$par->id);
            $cat->save();


            if($position == 0)
                $newPos = 0;
            else{
                $newPos = Category::find()->where(["parent_id"=>$cat->parent_id])
                    ->andWhere(["<>","id",$cat->id])->orderBy(["sort_id"=>SORT_ASC])->all()[$position-1]->sort_id;
                $newPos +=1;
            }
            $cat->sort_id = $newPos;
            $cat->save();

            $sort = Category::find()->where(["parent_id"=>$cat->parent_id])
                ->andWhere(["<>","id",$cat->id])
                ->andWhere([">=","sort_id",$newPos])->orderBy(["sort_id"=>SORT_ASC])->all();
            foreach ($sort  as $s){
                $s->sort_id = ++$newPos;
                $s->save();
            }

        }
        public function actionRename($id,$name){
            $cat = Category::findOne($id);
            if(!$cat)return;
            $cat->name = $name;
            $cat->save();
            return Json::encode([
                "id"=>$cat->id,
                "name"=>$cat->name
            ]);
        }
        public function actionAdd($id,$name,$position){
            $cat = new Category();
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
        $cats = Category::find()->all();

        return $this->render("index",[
            "title"=>"Категории",
            "catJson"=>Json::encode($cats)
        ]);
    }






}
