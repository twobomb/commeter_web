<?php

namespace app\controllers;

use app\models\Category;
use app\models\Item;
use app\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class ReportController extends AppController
{
    public $viewPath = '@app/views/';

    public function actionQuantitativeReport()
    {

        $departments = Yii::$app->user->identity->getAccessDepartments(false);
        $categories = Category::find()
            ->andWhere(['or', ['is_deleted' => 0], ['is_deleted' => null]])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('quantitative', [
            'departments' => $departments,
            'categories' => $categories,
        ]);
    }
    /**
     * Returns data for XLSX export (JSON matrix counts[cid][did]).
     * The XLSX file is generated on the client side using xlsx.full.min.js.
     */
    public function actionQuantitativeReportData()
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $availableDepartments = Yii::$app->user->identity->getAccessDepartments(false);
        $availableDepartmentIds = ArrayHelper::getColumn($availableDepartments, 'id');
        $availableDepartmentMap = [];
        foreach ($availableDepartments as $dep) {
            $availableDepartmentMap[(int)$dep->id] = $dep;
        }

        $availableCategories = Category::find()
            ->andWhere(['or', ['is_deleted' => 0], ['is_deleted' => null]])
            ->orderBy(['name' => SORT_ASC])
            ->all();
        $availableCategoryMap = [];
        foreach ($availableCategories as $cat) {
            $availableCategoryMap[(int)$cat->id] = $cat;
        }

        $departmentIds = array_map('intval', (array)Yii::$app->request->post('departments', []));
        $categoryIds = array_map('intval', (array)Yii::$app->request->post('categories', []));
        $removeEmptyCategories = Yii::$app->request->post('remove_empty_categories', '1');
        $removeEmptyCategories = (string)$removeEmptyCategories === '1' || (int)$removeEmptyCategories === 1;

        $departmentIds = array_values(array_intersect($departmentIds, $availableDepartmentIds));
        $categoryIds = array_values(array_intersect($categoryIds, array_keys($availableCategoryMap)));

        if (empty($departmentIds) || empty($categoryIds)) {
            return [
                'error' => 'Нужно выбрать хотя бы одно подразделение и одну категорию.',
                'departments' => [],
                'categories' => [],
                'counts' => [],
            ];
        }

        $countsRows = Item::find()
            ->select(['category_id', 'department_id', 'COUNT(*) AS qty'])
            ->where(['in', 'department_id', $departmentIds])
            ->andWhere(['in', 'category_id', $categoryIds])
            ->groupBy(['category_id', 'department_id'])
            ->asArray()
            ->all();

        $counts = [];
        foreach ($countsRows as $row) {
            $catId = (int)$row['category_id'];
            $depId = (int)$row['department_id'];
            $counts[$catId][$depId] = (int)$row['qty'];
        }

        $departments = [];
        foreach ($departmentIds as $depId) {
            if (isset($availableDepartmentMap[$depId])) {
                $departments[] = [
                    'id' => $depId,
                    'name' => $availableDepartmentMap[$depId]->name,
                ];
            }
        }

        $categories = [];
        foreach ($categoryIds as $catId) {
            if (isset($availableCategoryMap[$catId])) {
                $categories[] = [
                    'id' => $catId,
                    'name' => $availableCategoryMap[$catId]->name,
                ];
            }
        }

        if ($removeEmptyCategories) {
            $nonEmptyCategories = [];
            foreach ($categories as $cat) {
                $catId = (int)$cat['id'];
                $isNonEmpty = false;
                if (isset($counts[$catId]) && is_array($counts[$catId])) {
                    foreach ($departmentIds as $depId) {
                        if (isset($counts[$catId][$depId]) && (int)$counts[$catId][$depId] > 0) {
                            $isNonEmpty = true;
                            break;
                        }
                    }
                }
                if ($isNonEmpty) {
                    $nonEmptyCategories[] = $cat;
                }
            }
            $categories = $nonEmptyCategories;
        }

        return [
            'departments' => $departments,
            'categories' => $categories,
            'counts' => $counts,
        ];
    }
}

