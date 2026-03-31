<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $rows */
/** @var string $date24 */
/** @var string $date7 */

$this->title = 'Дашборд активности подразделений';
$this->params['breadcrumbs'][] = $this->title;

function formatActivityDate($date)
{
    if (empty($date)) {
        return 'Нет данных';
    }

    $diff = time() - strtotime($date);
    if ($diff < 0) {
        $diff = 0;
    }

    if ($diff < 3600) {
        $minutes = (int)floor($diff / 60);
        if ($minutes <= 0) {
            return 'только что';
        }
        return $minutes . ' мин назад';
    }

    if ($diff < 86400) {
        $hours = (int)floor($diff / 3600);
        $minutes = (int)floor(($diff % 3600) / 60);
        if ($minutes > 0) {
            return $hours . ' ч ' . $minutes . ' мин назад';
        }
        return $hours . ' ч назад';
    }

    if ($diff < 30 * 86400) {
        $days = (int)floor($diff / 86400);
        return $days . ' дн назад';
    }

    return date('d.m.Y H:i', strtotime($date));
}

function historyLink($department, $act, $dateFrom, $count)
{
    if ((int)$count <= 0) {
        return '<span class="text-muted">0</span>';
    }

    return Html::a(
        (string)$count,
        Url::to(['/admin/history', 'dep' => $department, 'act' => $act, 'date_from' => date('d.m.Y', strtotime($dateFrom))]),
        ['class' => 'fw-bold']
    );
}
?>

<div class="department-dashboard">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">
        Показаны подразделения с активностью за последние 7 дней (добавление, изменение, удаление).
    </p>

    <?php if (empty($rows)): ?>
        <div class="alert alert-info">За указанные периоды активность не найдена.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                <tr>
                    <th rowspan="2">Подразделение</th>
                    <th rowspan="2">Последняя активность</th>
                    <th colspan="3" class="text-center">Последние 24 часа</th>
                    <th colspan="3" class="text-center">Последние 7 дней</th>
                </tr>
                <tr>
                    <th class="text-center">Добавлено</th>
                    <th class="text-center">Изменено</th>
                    <th class="text-center">Удалено</th>
                    <th class="text-center">Добавлено</th>
                    <th class="text-center">Изменено</th>
                    <th class="text-center">Удалено</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= Html::encode($row['department']) ?></td>
                        <td><?= Html::encode(formatActivityDate($row['last_date'])) ?></td>
                        <td class="text-center"><?= historyLink($row['department'], 4, $date24, $row['create_24']) ?></td>
                        <td class="text-center"><?= historyLink($row['department'], 3, $date24, $row['change_24']) ?></td>
                        <td class="text-center"><?= historyLink($row['department'], 2, $date24, $row['delete_24']) ?></td>
                        <td class="text-center"><?= historyLink($row['department'], 4, $date7, $row['create_7']) ?></td>
                        <td class="text-center"><?= historyLink($row['department'], 3, $date7, $row['change_7']) ?></td>
                        <td class="text-center"><?= historyLink($row['department'], 2, $date7, $row['delete_7']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
