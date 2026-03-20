<?php

use app\models\History;
use yii\web\NotFoundHttpException;

function getSelectedDepartment(){
    $selDep = Yii::$app->session->get("SELECTED_DEPARTMENT");
    if($selDep != null)
        $selDep = \app\models\Department::findOne(["id"=>$selDep]);
    return $selDep;
}
function getSelectedCategory(){
    $selCat = Yii::$app->session->get("SELECTED_CATEGORY");
    if($selCat != null)
        $selCat = \app\models\Category::findOne(["id"=>$selCat]);
    return $selCat;
}

function var_dump_ret($mixed = null) {
    ob_start();
    var_dump($mixed);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function debug($s,$isClean =true){
    if($isClean)
        ob_clean();
    echo "<pre>";
    echo var_dump($s);
    echo "</pre>";
}
function getTimeAgo($date) {
$now = new DateTime();
    $past = new DateTime($date);
    $interval = $now->diff($past);

    if ($interval->y > 0) {
        return formatTimeAgo($interval->y, 'год', 'года', 'лет'). ' назад';
    } elseif ($interval->m > 0) {
        return formatTimeAgo($interval->m, 'месяц', 'месяца', 'месяцев'). ' назад';
    } elseif ($interval->d > 0) {
        return formatTimeAgo($interval->d, 'день', 'дня', 'дней'). ' назад';
    } elseif ($interval->h > 0) {
        return formatTimeAgo($interval->h, 'час', 'часа', 'часов') . ' ' .
            formatTimeAgo($interval->i, 'минуту', 'минуты', 'минут') . ' назад';
    } elseif ($interval->i > 0) {
        return formatTimeAgo($interval->i, 'минуту', 'минуты', 'минут') . ' назад';
    } else {
        return 'только что';
    }
}
function sortByHierarchy(array $elements, $parentId = 0) {
    $sorted = [];

    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            // Добавляем текущий элемент
            $sorted[] = $element;
            // Рекурсивно добавляем его детей
            $children = sortByHierarchy($elements, $element['id']);
            $sorted = array_merge($sorted, $children);
        }
    }

    return $sorted;
}
function recalculate( &$elements, $parentId = null) {
    $cnt = 0;
    foreach ($elements as &$element) {
        if ($element['parent_id'] == $parentId) {
            $element["count"] += recalculate($elements,$element["id"]);
            $cnt+=$element["count"];
        }
    }
    return $cnt;
}

function buildTree(array &$elements, $parentId = 0) {
    $branch = [];

    foreach ($elements as &$element) {
        if ($element['parent_id'] == $parentId) {
            // Находим детей текущего элемента
            $children = buildTree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
            // Удаляем элемент из массива, чтобы не обрабатывать его повторно
            unset($element);
        }
    }
    return $branch;
}

function formatTimeAgo($number, $one, $two, $many) {
    if ($number % 10 == 1 && $number % 100 != 11) {
        return "$number $one";
    } elseif ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20)) {
        return "$number $two";
    } else {
        return "$number $many";
    }
}

function getUrlBack(){
    $url = '/item/massive-transfer';
    if(isset($_GET['backtype']) && $_GET['backtype'] == 'change_cat')
        $url = '/item/massive-change-category';
    return $url;
}
function saveHistory(\app\models\Item $item,$stateBefore,$act = null){
    try {
        $stateAfter = GetItemFullState($item);
        $action = "change";
        if(count($stateBefore) == 0)
            $action = "create";
        if($act != null)
            $action = $act;
        $vals = array_diff_assoc($stateAfter,$stateBefore);
        $h = new History();
        $h->action = $action;
        $h->id = $item->id;
        $h->name = $item->name;
        $h->category = $item->category->name;
        $h->department = $item->department->name;
        $h->inv_num = $item->inv_num;
        $h->data = json_encode($vals,JSON_UNESCAPED_UNICODE);
        if(count($vals) > 0)
            $h->save();
    }catch (Exception  $exception){}
}
function saveAnyHistory(\app\models\Item $item,$data,$act ){
    try {
        $action = $act;
        $h = new History();
        $h->action = $action;
        $h->id = $item->id;
        $h->name = $item->name;
        $h->category = $item->category->name;
        $h->department = $item->department->name;
        $h->inv_num = $item->inv_num;
        $h->data = json_encode($data,JSON_UNESCAPED_UNICODE);
        if(count($data) > 0)
            $h->save();
    }catch (Exception  $exception){}
}
function GetItemFullState(\app\models\Item $item){
   $attrs = $item->getAttributes();
    $vals = [];
    $keys = array_keys($attrs);

    foreach ($keys as $k) {
        switch ($k){
            case "department_id":
                $vals["Подразделение"] = $item->department->name;
                break;
            case "employee_id":
                $vals["Сотрудник"] = $item->employee == null?"Не выбрано":$item->employee->fullName;
                break;
            case "category_id":
                $vals["Категория"] = $item->category->name;
                break;
            case "responsible_employee_id":
                $vals["Материально ответственный"] = ($item->responsibleEmployee == null?"Не выбрано":$item->responsibleEmployee->fullName);
                break;
            case "date_change":
                break;
            default:
                $vals[$item->getAttributeLabel($k)] = $item[$k];
        }
    }
    foreach($item->featureValues as $fv){

        setlocale(LC_ALL, 'ru_RU.UTF-8');
        $n =$fv->feature->name; // preg_replace("/[^а-яА-Яa-zA-Z0-9 ]+/", "*", $fv->feature->name);
        $vals[$n] = $fv->getDisplayValue();
    }
    return $vals;
}
function my_base64_decode( $data ) {	// Decodes data encoded with MIME base64
    $b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    $i=0;
    $enc='';

    do {  // unpack four hexets into three octets using index points in b64
        $h1 = strrpos($b64,substr($data,$i++,1));
        $h2 = strrpos($b64,substr($data,$i++,1));
        $h3 = strrpos($b64,substr($data,$i++,1));
        $h4 = strrpos($b64,substr($data,$i++,1));

        $bits = $h1<<18 | $h2<<12 | $h3<<6 | $h4;

        $o1 = $bits>>16 & 0xff;
        $o2 = $bits>>8 & 0xff;
        $o3 = $bits & 0xff;

        if ($h3 == 64)	  $enc .= chr($o1);
        else if ($h4 == 64) $enc .= chr($o1). chr($o2);
        else			   $enc .= chr($o1). chr($o2). chr($o3);
    } while ($i < strlen($data));

    return $enc;
}
function isShowAllItems(){
    return \Yii::$app->session->get("MODE_IS_SHOW_ALL","1") == "1";
}
function nowdate(){
    return date('Y-m-d H:i:s');
}
function returnBack(){
    if(isset($_SERVER['HTTP_REFERER']))
        return Yii::$app->response->redirect($_SERVER['HTTP_REFERER']);
    else
        return Yii::$app->response->redirect("/");
}

function saveUniFile($pathToSave,$tmpFile,$name,$options = []){
    $ext = pathinfo($name)["extension"];
    if(isset($options["extension"]) && !in_array($ext,$options["extension"]))
        throw new \yii\web\ForbiddenHttpException("Расширение файла ".$ext." доступные расширения(".implode(",",$options["extension"]).")");

    if(!file_exists($pathToSave))
        throw new \yii\web\ForbiddenHttpException("Не существующая директория ".$pathToSave);
    do {
        $filename = md5(microtime() . rand(0, 9999)) . "." . $ext;
    } while (file_exists($pathToSave . $filename));
    $filepath = $pathToSave . $filename;
    if(!move_uploaded_file($tmpFile, $filepath))
        throw new \yii\web\ForbiddenHttpException("Ошибка перемещения файла '".$tmpFile ."' в '". $filepath."''");
    return $filepath;
}

function copyUniFile($pathToSave,$current_filepath,$options = []){
    $ext = pathinfo($current_filepath)["extension"];
    if(isset($options["extension"]) && !in_array($ext,$options["extension"]))
        throw new \yii\web\ForbiddenHttpException("Расширение файла ".$ext." доступные расширения(".implode(",",$options["extension"]).")");

    if(!file_exists($pathToSave))
        throw new \yii\web\ForbiddenHttpException("Не существующая директория ".$pathToSave);
    do {
        $filename = md5(microtime() . rand(0, 9999)) . "." . $ext;
    } while (file_exists($pathToSave . $filename));
    $filepath = $pathToSave . $filename;
    if(!copy($current_filepath, $filepath))
        throw new \yii\web\ForbiddenHttpException("Ошибка копирования файла '".$current_filepath ."' в '". $filepath."''");
    return $filepath;
}
function getUniName($dir,$ext){// exm: cache/img/
    do {
        $filename = md5(microtime() . rand(0, 9999)) . "." . $ext;
    } while (file_exists($dir . $filename));
    return $dir . $filename;

}
function rmRec($path) {
    if (is_file($path)) return unlink($path);
    if (is_dir($path)) {
        foreach(scandir($path) as $p) if (($p!='.') && ($p!='..'))
            rmRec($path.DIRECTORY_SEPARATOR.$p);
        return rmdir($path);
    }
    return false;
}

function unlink_if_exists($path){
    if(file_exists($path))
        unlink($path);
}
function showError($msg,$title ="Ошибка",$delay = 3000){
    showMessage($msg,"danger",$title,$delay);
}
function showMessage($msg,$type = "success",$title ="Сообщение",$delay = 3000){//info,danger,success,warning
    $types = ["info","danger","success","warning"];
    if(!in_array($type,$types))
        $type = $types[0];
    \Yii::$app->session->addFlash("alert",["message"=>$msg,"type"=>$type,"delay"=>$delay,"title"=>$title]);
}
