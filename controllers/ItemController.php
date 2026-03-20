<?php

namespace app\controllers;

use app\models\Category;
use app\models\Department;
use app\models\Feature;
use app\models\FeatureValue;
use app\models\Item;
use app\models\ItemSearch;
use app\models\Repair;
use app\models\search\RepairSearch;
use app\models\search\TransferSearch;
use app\models\Tag;
use app\models\Transfer;
use app\models\User;
use kartik\detail\DetailView;
use Yii;
use yii\base\BaseObject;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * ItemController implements the CRUD actions for Item model.
 */
class ItemController extends AppController
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
    public function actionSelectItem($id,$val){
        $item = Item::findOne(["id"=>$id]);
        if($item){
            $items = \Yii::$app->session->get("SELECTED_TRANSFER_ITEMS",[]);
            if(!is_array($items))$items = [];
            if($val === "0" && in_array($item->id,$items))
                $items = array_diff($items, [$item->id]);
            if($val === "1" && !in_array($item->id,$items))
                $items[]= $item->id;
            \Yii::$app->session->set("SELECTED_TRANSFER_ITEMS",$items);
            if(!\Yii::$app->request->isAjax)
                return returnBack();
            return "OK";
        }
    }
    /**
     * Lists all Item models.
     *
     * @return string
     */
    public function actionIndex(){

        if(Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
        //debug($_GET);die;
        $searchModel = new ItemSearch();

        $dep = getSelectedDepartment();
        if($dep == null) {
            if(count(\Yii::$app->user->identity->departmentsAccess) == 0)
                throw new ForbiddenHttpException("Доступ запрещен");
            $dep = Department::findOne(["id"=>\Yii::$app->user->identity->departmentsAccess[0]]);

            \Yii::$app->session->set("SELECTED_DEPARTMENT",$dep->id);
        }
        $searchModel->department_id = $dep->id;

        $cat = getSelectedCategory();
        if($cat == null) {
            $cat = Category::find()->one();
            if($cat == null)
                throw new ForbiddenHttpException("Доступ запрещен");

            \Yii::$app->session->set("SELECTED_CATEGORY",$cat->id);
        }
        $searchModel->category_id = $cat->id;

        $dataProvider = $searchModel->search($this->request->queryParams);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionSetModeShow($mode){
        if(!in_array($mode,["1","0"]))
            throw new ForbiddenHttpException("Неизвестное значение");

        \Yii::$app->session->set("MODE_IS_SHOW_ALL",$mode);
        return "OK";
    }
    public function actionSelectDep($id){
        $dep = Department::findOne(["id"=>$id]);
        if(!$dep)
            throw new ForbiddenHttpException("Подразеделение не найдено!");
        if(!$dep->canAccess())
            throw new ForbiddenHttpException("Доступ запрещен!");

        \Yii::$app->response->format = Response::FORMAT_JSON;
        \Yii::$app->session->set("SELECTED_DEPARTMENT",$dep->id);
        return [
            "id"=>$dep->id,
            "name"=>$dep->name
        ];
    }
    public function actionSelectCategory($id){
        $dep = Category::findOne(["id"=>$id]);
        if(!$dep)
            throw new ForbiddenHttpException("Категория не найдена!");
        \Yii::$app->response->format = Response::FORMAT_JSON;
        \Yii::$app->session->set("SELECTED_CATEGORY",$dep->id);
        return [
            "id"=>$dep->id,
            "name"=>$dep->name
        ];
    }
    /**
     * Displays a single Item model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if(!$model->department->canAccess())
            throw new ForbiddenHttpException("Доступ запрещен");
        return $this->render('view', [
            'model' => $model,
        ]);
    }
    public function actionGetCountAll(){
        $ids = [];
        if(isShowAllItems())
            $ids = getSelectedDepartment()->unit->getIdsAllUnitDepartmentsWithUnit();
        else
            $ids = [getSelectedDepartment()->id];

        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand("SELECT id,parent_id,
(SELECT COUNT(*) FROM item WHERE item.category_id = category.id AND item.department_id IN (".implode(",",$ids).") ) as count
 FROM category");
        $all =$command->queryAll();


        recalculate($all);

        $result = ArrayHelper::map($all,"id","count");

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    public function actionDuplicate(){
        if(\Yii::$app->request->isPost){
            $from = intval($_POST["from"]);
            $to = intval($_POST["to"]);
            $count = intval($_POST["countDup"]);
            $type= $_POST["type_duplicate"];
			
			
            if($type == "inv" && $from  > $to)
                throw new ServerErrorHttpException("Значение до должно быть больше чем от!");
			
            if($type == "col" && $count <= 0)
                throw new ServerErrorHttpException("Значение количества должно быть больше 0!");
			
			if($type == "col"){
				$from = 0;
				$to = $from+$count-1;
			}
			
            $dep  = Department::findOne(["id"=>$_POST["department_id"]]);
            if($dep == null)
                throw new ServerErrorHttpException("Подразделение не найдено!");

            $item  = Item::findOne(["id"=>$_POST["id"]]);
            if($item == null)
                throw new ServerErrorHttpException("Item не найдено!");
			
					
			if(!in_array($item->department_id,ArrayHelper::getColumn(\Yii::$app->user->identity->getAccessDepartments(false), 'id')))
				throw new ForbiddenHttpException("У вас нет доступа к департаменту!");

            if($to - $from >= 2000)
                throw new ServerErrorHttpException("Слишком много дублирований!");

            $ok = 0;

            for ($i = $from; $i <= $to;$i++){
                $clone = new Item();
                $clone->attributes = $item->attributes;
                $clone->id = null;
                $clone->department_id = $dep->id;
                $clone->employee_id = null;
				if($type == "inv")
					$clone->inv_num = "$i";
                $clone->isNewRecord = true;
                if($clone->save()){
                    $ok++;
                    foreach ($item->featureValues as $fv){
                        $cloneFv = new FeatureValue();
                        $cloneFv->attributes = $fv->attributes;
                        $cloneFv->id = null;
                        $cloneFv->item_id = $clone->id;
                        $cloneFv->isNewRecord = true;
                        $cloneFv->save();
                    }
                    saveHistory($clone,[]);
                }
            }
            showMessage("Добавлено средств: $ok ");
            return $this->redirect("/item/index");
        }
		
		
			$model = $this->findModel($_GET["id"]);
		if(!in_array($model->department_id,ArrayHelper::getColumn(\Yii::$app->user->identity->getAccessDepartments(false), 'id')))
			throw new ForbiddenHttpException("У вас нет доступа к департаменту!");
        return $this->render('duplicate', [
            'model' => $model,
        ]);
    }


    public function actionDetailItem(){

        if (isset($_POST['expandRowKey'])) {
            $d = Item::findOne(["id"=>$_POST['expandRowKey']]);

            return $this->renderAjax('view', [
                'model' =>$d
            ]);
        } else {
            return '<div class="alert alert-danger">Не найдено</div>';
        }
    }

    public function actionCanAddToCategory(){
        $cat = getSelectedCategory();
        if($cat != null && $cat->is_can_add)
                return "OK";
        return "NO";
    }
    public function actionDetailTransfer(){

        if (isset($_POST['expandRowKey'])) {
            $trans = Transfer::findOne(["id"=>$_POST['expandRowKey']]);
            return DetailView::widget([
                'model' => $trans,
                'attributes' => [
                    'id',
                    'date:date',
                    'description:ntext',

                    [
                        'attribute'=>'department_id_from',
                        'format'=>'raw',
                        'value'=>(function($m){
                            return $m->departmentFrom->name;
                        })($trans)
                    ],
                    [
                        'attribute'=>'department_id_to',
                        'format'=>'raw',
                        'value'=>(function($m){
                            return $m->departmentTo->name;
                        })($trans)
                    ],
                    [
                        'label' => 'Список средств',
                        'format'=>'raw',
                        'value'=>(function($m){
                            $s= "<ul>";
                            foreach ($m->items as $it)
                                $s.= '<li>'.Html::a($it->name. " ($it->inv_num)","/item/view?id=$it->id",['target'=>"_blank"]).'</li>';
                            $s.='</ul>';
                            return $s;
                        })($trans)
                    ],
                    'date_change:datetime',
                ],
            ]);
        } else {
            return '<div class="alert alert-danger">Не найдено</div>';
        }
    }
    public function actionRepairDelete($id){
        $rep = Repair::findOne(["id"=>$id]);
        if($rep->delete())
            showMessage("Удалено");
        return returnBack();
    }
    public function actionDetailRepair(){

        if (isset($_POST['expandRowKey'])) {
            $trans = Repair::findOne(["id"=>$_POST['expandRowKey']]);
            return DetailView::widget([
                'model' => $trans,
                'attributes' => [
                    'id',

                    [
                        'attribute'=>'type',
                        'format'=>'raw',
                        'value'=>(function(Repair $m){
                            return $m->DisplayTypeName();
                        })($trans)
                    ],
                    'sum',
                    'date:date',
                    [
                        'attribute'=>'item_id',
                        'format'=>'raw',
                        'value'=>(function($m){
                            return Html::a($m->item->name,"/item/view?id=$m->item_id",['target'=>"_blank"]);
                        })($trans)
                    ],
                    'description:ntext',
                    'date_change:datetime',
                ],
            ]);
        } else {
            return '<div class="alert alert-danger">Не найдено</div>';
        }
    }
    public function actionTransferList(){
        $searchModel = new TransferSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('/item/transfer-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionRepairList(){
        $searchModel = new RepairSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('/item/repair-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionDeleteSelected(){
        if(Yii::$app->request->isPost){
            $items = Item::find()->where(["in",'id',Yii::$app->request->post("ids",[])])->all();
            $cnt = 0;
            foreach ($items as $item){
                $c = $item->delete();
                if($c)
                    $cnt+=$c;
            }
            showMessage("Удалено средств: $cnt");
        }
        return "";
    }
    public function actionAddSelectedItems(){
        if(Yii::$app->request->isPost) {
            $itemsToAdd = ArrayHelper::getColumn(Item::find()->where(["in",'id',Yii::$app->request->post("ids",[])])->all(),"id");
            $items = \Yii::$app->session->get("SELECTED_TRANSFER_ITEMS", []);
            if (!is_array($items)) $items = [];
            $cnt = count($items);
            $items =array_unique(array_merge($items,$itemsToAdd));
            $cnt = count($items) - $cnt;
            \Yii::$app->session->set("SELECTED_TRANSFER_ITEMS", $items);
            showMessage("Выделено новых средств: $cnt");
        }
        return "";
    }
    public function actionMassiveChangeCategory()
    {
        if(Yii::$app->request->isPost){
            $cat = Category::findOne(["id"=>$_POST["category_id"]]);
            if(!$cat->is_can_add)
                throw new ForbiddenHttpException("В эту категорию запрещено добавлять!");
                $items = Item::find()->where(["in","id",\Yii::$app->session->get("SELECTED_TRANSFER_ITEMS",[])])->all();
                if(count($items) == 0)
                    throw new ForbiddenHttpException("Не выбрано ни одного средства");

            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                foreach ($items as $it){
                    $stateBefore = GetItemFullState($it);
                    $it->category_id = $cat->id;
                    if($it->save())
                        saveHistory($it,$stateBefore);
                    else
                        throw new ServerErrorHttpException("Произошла ошибка при сохранении!");
                }
                $transaction->commit();
                showMessage("Сохранено");
                \Yii::$app->session->set("SELECTED_TRANSFER_ITEMS",[]);
                return $this->redirect("/item/index");

            } catch(\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        return $this->render('massive-change-category', [
        ]);
    }
    public function actionMassiveTransfer(){
        $transfer = new Transfer();
        $transfer->type = "external";
        $transfer->date = date("d.m.Y");

        $skipResposible = false;
        if(User::isAdmin() &&  isset($_POST["adminTmpOption"]) && $_POST["adminTmpOption"] === "1")
            $skipResposible = true;

        $items = \Yii::$app->session->get("SELECTED_TRANSFER_ITEMS",[]);
        if(count($items) > 0 && $fi = Item::findOne(["id"=>array_shift($items)]))
            $transfer->department_id_from =$fi->department->unit->id;
        ///убирать employee_id и workspace
        ///
        if ($this->request->isPost) {
            $transfer->load($this->request->post());
            if(!$skipResposible && $transfer->responsible_employee_id_to == null)
                $transfer->addError("responsible_employee_id_to","Необходимо выбрать мат.ответственного!");

            if($transfer->department_id_to == $transfer->department_id_from)
                $transfer->addError("department_id_to","Нельзя перемещать в тоже подразделение!");
            $transfer->ignoreAllow = true;
            if($transfer->date != null)
                $transfer->date = date("Y-m-d",strtotime($transfer->date));
            $itemTrans = [];
            if(isset($_POST["items"]) && is_array($_POST["items"]))
                $itemTrans = Item::find()->where(["in","id",$_POST["items"]])->all();
            if(count($itemTrans) == 0)
                $transfer->addError("department_id_to","Нужно добавить хотя-бы одно перемещаемое средство!");

            if($skipResposible && $transfer->responsible_employee_id_to == null)
                $transfer->responsible_employee_id_to = $itemTrans[0]->responsible_employee_id;

            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                if($transfer->validate(null,false) && $transfer->save(false)){
                    foreach ($itemTrans as $it){
                        $transfer->link("items",$it);
                        $stateBefore = GetItemFullState($it);
                        $it->ignoreAllow = true;
                        $it->employee_id = null;
                        $it->workspace = null;
                        $it->department_id = $transfer->department_id_to;
                        if(!$skipResposible)
                            $it->responsible_employee_id= $transfer->responsible_employee_id_to;
                        if(!$it->save())
                            throw new ForbiddenHttpException($it->errors);
                        saveHistory($it, $stateBefore, "transfer");
                    }
                    $transaction->commit();
                    showMessage("Сохранено");
                    \Yii::$app->session->set("SELECTED_TRANSFER_ITEMS",[]);

                    return $this->redirect("/item/index");

                }
                else $transfer->loadDefaultValues();

            } catch(\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        return $this->render('massive-transfer', [
            'model' => $transfer,
        ]);
    }
    public function actionGetListResponsibles($id){
        if($d = Department::findOne(["id"=>$id])){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            return \yii\helpers\ArrayHelper::map($d->responsibles,"id","fullName");
        }
        throw new NotFoundHttpException("Dep не найден");
    }
    public function actionSearch(){
        $searchModel = new ItemSearch();
        $dataProvider = $searchModel->searchAll($this->request->queryParams);


        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionTransfer($id){

        $is_partial = isset($_GET["partial"]) && $_GET["partial"] == "1";
        $item = $this->findModel($id);
		
		
			if(!in_array($item->department_id,ArrayHelper::getColumn(\Yii::$app->user->identity->getAccessDepartments(false), 'id')))
				throw new ForbiddenHttpException("У вас нет доступа к департаменту!");
			
        $transfer = new Transfer();
        $transfer->type = "internal";
        $transfer->department_id_from = $item->department_id;
        $transfer->department_id_to = $item->department_id;
        $transfer->workplace_from = $item->workspace;
        $transfer->date = date("d.m.Y");
        if ($this->request->isPost) {

            $transfer->load($this->request->post());
            if(!empty($transfer->date))
                $transfer->date = date("Y-m-d",strtotime($transfer->date));

            if ($transfer->save()) {
                $transfer->link("items",$item);
                $stateBefore = GetItemFullState($item);
                $item->ignoreAllow = true;
                $item->workspace = $transfer->workplace_to;
                $item->employee_id = $transfer->employee_to_id;
                if($item->department_id != $transfer->department_id_to)
                    $item->department_id = $transfer->department_id_to;
                if ($item->save()) {
                    saveHistory($item, $stateBefore, "transfer");

                    if(\Yii::$app->request->isAjax)
                        return "OK";
                    showMessage("Перемещено");
                    return $this->redirect("/item/index");
                } else {
                    $transfer->delete();
                }
            } else
                $transfer->loadDefaultValues();
        }
        if($is_partial)
            return $this->renderAjax('transfer', [
                'model' => $transfer,
                'item' => $item,
                'is_partial'=>$is_partial
            ]);
        return $this->render('transfer', [
            'model' => $transfer,
            'item' => $item,
            'is_partial'=>$is_partial
        ]);
    }
    public function actionRepair($id){

        $is_partial = isset($_GET["partial"]) && $_GET["partial"] == "1";
        $item = $this->findModel($id);
        $repair = new Repair();
        $repair->item_id = $id;
        $repair->sum = 0;
        $repair->date = date("d.m.Y");
        if ($this->request->isPost) {
            $repair->load($this->request->post());
            if(!empty($repair->date))
                $repair->date = date("Y-m-d",strtotime($repair->date));

            if ($repair->save()) {
                    saveAnyHistory($item, [
                        "Тип обслуживания"=>$repair->DisplayTypeName(),
                        "Дополнительная информация"=>$repair->description,
                        "Сумма"=>$repair->sum,
                        "Дата обслуживания"=>\Yii::$app->formatter->asDate($repair->date)
                    ], "repair");

                    if(\Yii::$app->request->isAjax)
                        return "OK";
                    showMessage("Сохранено");
                return $this->redirect("/item/index");
            } else
                $repair->loadDefaultValues();
        }



        if($is_partial)
            return $this->renderAjax('repair', [
                'model' => $repair,
                'item' => $item,
                'is_partial'=> $is_partial
            ]);
        return $this->render('repair', [
            'model' => $repair,
            'item' => $item,
            'is_partial'=> $is_partial
        ]);
    }
    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $is_partial = isset($_GET["partial"]) && $_GET["partial"] == "1";
        $model = new Item();
        $dep = getSelectedDepartment();
        if($dep == null) {
            showMessage("Сначала выберите подразделение!","danger","Ошибка");
            return  \Yii::$app->response->redirect("/item/index");;
        }
        $model->department_id = $dep->id;

        $cat = getSelectedCategory();
        if($cat == null) {
            showMessage("Сначала выберите категорию!","danger","Ошибка");
            return  \Yii::$app->response->redirect("/item/index");
        }


        if(!$cat->is_can_add){
            {
                showMessage("Добавление в  категорию '{$cat->name}' запрещено!","warning","Предупреждение");
                return  \Yii::$app->response->redirect("/item/index");;
            }
        }

        $model->category_id = $cat->id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                if($model->feats == null)
                    $model->feats = [];
                if($model->edittags == null)
                    $model->edittags = [];

                foreach ($model->feats as $fid=>$fv){
                    $fff = Feature::findOne(["id"=>$fid]);
                    $f = new FeatureValue();
                    $f->feature_id = $fid;
                    $f->value = $fv;
                    $f->item_id = $model->id;
                    if(!$f->save()){
                        showError("Не удалось сохранить свойство "+$fff->name);
                    }
                }
                foreach ($model->edittags as $tagid){
                    $tg = Tag::findOne(["id"=>$tagid]);
                    $tg->link("items",$model);
                }
                saveHistory($model,[]);
                if(\Yii::$app->request->isAjax)
                    return "OK";
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        if($is_partial)
            return $this->renderAjax('create', [
                'model' => $model,
                'is_partial'=>$is_partial
            ]);
        return $this->render('create', [
            'model' => $model,
            'is_partial'=>$is_partial
        ]);
    }
    public function actionHistory($id){

        return $this->render('history', [
            'id' => $id
        ]);
    }
    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id){

        $is_partial = isset($_GET["partial"]) && $_GET["partial"] == "1";
        $model = $this->findModel($id);
		
		
		if(!in_array($model->department_id,ArrayHelper::getColumn(\Yii::$app->user->identity->getAccessDepartments(false), 'id')))
			throw new ForbiddenHttpException("У вас нет доступа к департаменту!");

        $model->feats = [];
        foreach ($model->featureValues as $fv)
            $model->feats[$fv->feature_id] = $fv->value;


        $model->edittags = [];
        foreach ($model->tags as $tg)
            $model->edittags[] = $tg->id;
        $stateBefore = GetItemFullState($model);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            if($model->feats == null)
                $model->feats = [];
            if($model->edittags == null)
                $model->edittags = [];

            $model->unlinkAll("featureValues",true);
            foreach ($model->feats as $fid=>$fv){
                $fff = Feature::findOne(["id"=>$fid]);
                $f = new FeatureValue();
                $f->feature_id = $fid;
                $f->value = $fv;
                $f->item_id = $model->id;
                if(!$f->save()){
                    showError("Не удалось сохранить свойство "+$fff->name);
                }
            }
            $model->unlinkAll("tags",true);
            foreach ($model->edittags as $tagid){
                $tg = Tag::findOne(["id"=>$tagid]);
                $tg->link("items",$model);
            }
            saveHistory($model,$stateBefore);
            if(\Yii::$app->request->isAjax)
                return "OK";
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if($is_partial)
            return $this->renderAjax('update', [
                'model' => $model,
                'is_partial'=>$is_partial
            ]);
        return $this->render('update', [
            'model' => $model,
            'is_partial'=>$is_partial
        ]);
    }

    /**
     * Deletes an existing Item model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $m = $this->findModel($id);
				
		if(!in_array($m->department_id,ArrayHelper::getColumn(\Yii::$app->user->identity->getAccessDepartments(false), 'id')))
			throw new ForbiddenHttpException("У вас нет доступа к департаменту!");

        if($m->delete()){
            showMessage("Удалено '$m->name'");
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Item::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
