<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Department[] $departments */
/** @var app\models\Category[] $categories */

$this->title = 'Количественный отчет';
$this->params['breadcrumbs'][] = 'Отчеты';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="quantitative-report-page">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">Выберите подразделения и категории, затем сформируйте Excel-отчет.</p>

    <form method="post" action="/report/quantitative-report-data" onsubmit="return false;">
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Подразделения</strong>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="toggleCheckboxes('dep-box', true)">Выбрать все</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleCheckboxes('dep-box', false)">Снять выделение</button>
                </div>
            </div>
            <div class="card-body" id="dep-box">
                <input
                    type="text"
                    class="form-control mb-3"
                    placeholder="Поиск подразделения..."
                    oninput="filterList('dep-list', this.value)"
                >
                <div id="dep-list" class="list-group" style="max-height: 320px; overflow: auto;">
                    <?php foreach ($departments as $dep): ?>
                        <label class="list-group-item d-flex align-items-center dep-item" data-name="<?= htmlspecialchars(mb_strtolower($dep->name, 'UTF-8')) ?>">
                            <input class="form-check-input me-2" type="checkbox" name="departments[]" value="<?= (int)$dep->id ?>">
                            <span><?= Html::encode($dep->name) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Категории</strong>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="toggleCheckboxes('cat-box', true)">Выбрать все</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleCheckboxes('cat-box', false)">Снять выделение</button>
                </div>
            </div>
            <div class="card-body" id="cat-box">
                <input
                    type="text"
                    class="form-control mb-3"
                    placeholder="Поиск категории..."
                    oninput="filterList('cat-list', this.value)"
                >
                <div id="cat-list" class="list-group" style="max-height: 320px; overflow: auto;">
                    <?php foreach ($categories as $cat): ?>
                        <label class="list-group-item d-flex align-items-center cat-item" data-name="<?= htmlspecialchars(mb_strtolower($cat->name, 'UTF-8')) ?>">
                            <input class="form-check-input me-2" type="checkbox" name="categories[]" value="<?= (int)$cat->id ?>">
                            <span><?= Html::encode($cat->name) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="form-check mb-3">
            <input
                id="remove_empty_categories"
                class="form-check-input"
                type="checkbox"
                name="remove_empty_categories"
                value="1"
                checked
            >
            <label class="form-check-label" for="remove_empty_categories">Убрать пустые категории</label>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <button id="btn-preview-quantitative" class="btn btn-outline-primary btn-lg" type="button">
                Предпросмотр
            </button>
            <button id="btn-export-quantitative" class="btn btn-success btn-lg" type="button">
                <i class="fa fa-file-excel-o"></i> Выгрузить в Excel
            </button>
        </div>
    </form>
</div>

<style>
    #previewModal {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        z-index: 9999;
        display: none;
        padding: 24px;
    }
    #previewModal .modal-content {
        background: #fff;
        border-radius: 10px;
        max-width: 95vw;
        max-height: 92vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    #previewModal .modal-header {
        padding: 12px 16px;
        border-bottom: 1px solid #e9eef5;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    #previewModal .modal-body {
        padding: 12px 16px;
        overflow: auto;
    }
    #previewTable {
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }
    #previewTable th, #previewTable td {
        border: 1px solid #444;
        padding: 6px 8px;
        white-space: nowrap;
    }
    #previewTable th.sticky-top {
        position: sticky;
        top: 0;
        z-index: 3;
        background: #e9eef5;
    }
    #previewTable th.sticky-col {
        position: sticky;
        left: 0;
        z-index: 2;
        background: #f8f9fb;
        font-weight: bold;
    }
    #previewTable th.sticky-top.sticky-col {
        z-index: 4;
    }
</style>

<div id="previewModal">
    <div class="modal-content">
        <div class="modal-header">
            <div><strong>Предпросмотр отчета</strong></div>
            <button class="btn btn-sm btn-success" type="button" onclick="closePreview()">Закрыть</button>
        </div>
        <div class="modal-body">
            <div id="previewTableWrap"></div>
        </div>
    </div>
</div>

<script src="/js/xlsx.full.min.js"></script>
<script>
function toggleCheckboxes(containerId, checked) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.querySelectorAll('input[type="checkbox"]').forEach((el) => {
        el.checked = checked;
    });
}

function filterList(listId, query) {
    const list = document.getElementById(listId);
    if (!list) return;
    const q = (query || '').toLowerCase().trim();
    list.querySelectorAll('.list-group-item').forEach((row) => {
        const name = (row.dataset.name || row.textContent || '').toLowerCase();
        row.setAttribute("style",name.includes(q) ? '' : 'display:none !important;');
    });
}

function getSelectedIds(checkboxSelector) {
    return Array.from(document.querySelectorAll(checkboxSelector + ':checked')).map((el) => Number(el.value));
}

function getRemoveEmptyCategoriesValue() {
    const el = document.getElementById('remove_empty_categories');
    return (el && el.checked) ? 1 : 0;
}

const btnExport = document.getElementById('btn-export-quantitative');
if (btnExport) {
btnExport.addEventListener('click', async () => {
    const depIds = getSelectedIds('input[name="departments[]"]');
    const catIds = getSelectedIds('input[name="categories[]"]');

    if (depIds.length === 0 || catIds.length === 0) {
        alert('Нужно выбрать минимум одно подразделение и одну категорию.');
        return;
    }

    const csrf = document.querySelector('input[name="_csrf"]').value;

    const params = new URLSearchParams();
    params.append('_csrf', csrf);
    depIds.forEach((id) => params.append('departments[]', String(id)));
    catIds.forEach((id) => params.append('categories[]', String(id)));
    params.append('remove_empty_categories', String(getRemoveEmptyCategoriesValue()));

    const resp = await fetch('/report/quantitative-report-data', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString(),
    });

    const data = await resp.json();
    if (data.error) {
        alert(data.error);
        return;
    }
    if (!data.categories || data.categories.length === 0) {
        alert('Нет данных по выбранным параметрам.');
        return;
    }

    const aoa = [];
    aoa.push(['Категория \\ Подразделение', ...data.departments.map((d) => d.name)]);

    data.categories.forEach((cat) => {
        const row = [cat.name];
        data.departments.forEach((dep) => {
            let v = 0;
            if (data.counts && data.counts[cat.id] && data.counts[cat.id][dep.id] != null) {
                v = data.counts[cat.id][dep.id];
            }
            row.push(Number(v));
        });
        aoa.push(row);
    });

    const ws = XLSX.utils.aoa_to_sheet(aoa);

    // Закрепляем верхнюю строку и левую колонку (A и B по Excel координатам).
    ws['!freeze'] = {
        xSplit: '1',
        ySplit: '1',
        topLeftCell: 'B2',
        activePane: 'bottomRight',
        state: 'frozen',
    };

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Отчет');

    const fileName = 'quantitative-report-' + new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-') + '.xlsx';
    XLSX.writeFile(wb, fileName);
});
}

function showPreviewModal() {
    const modal = document.getElementById('previewModal');
    if (!modal) return;
    modal.style.display = 'block';
}

function closePreview() {
    const modal = document.getElementById('previewModal');
    if (modal) modal.style.display = 'none';
    const wrap = document.getElementById('previewTableWrap');
    if (wrap) wrap.innerHTML = '';
}

async function fetchQuantitativeReportData() {
    const depIds = getSelectedIds('input[name="departments[]"]');
    const catIds = getSelectedIds('input[name="categories[]"]');

    if (depIds.length === 0 || catIds.length === 0) {
        alert('Нужно выбрать минимум одно подразделение и одну категорию.');
        return null;
    }

    const csrf = document.querySelector('input[name="_csrf"]').value;

    const params = new URLSearchParams();
    params.append('_csrf', csrf);
    depIds.forEach((id) => params.append('departments[]', String(id)));
    catIds.forEach((id) => params.append('categories[]', String(id)));
    params.append('remove_empty_categories', String(getRemoveEmptyCategoriesValue()));

    const resp = await fetch('/report/quantitative-report-data', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString(),
    });

    const data = await resp.json();
    if (data.error) {
        alert(data.error);
        return null;
    }
    return data;
}

function buildPreviewTable(data) {
    const departments = data.departments || [];
    const categories = data.categories || [];
    const counts = data.counts || {};

    let html = '';
    html += '<table id="previewTable">';
    html += '<thead><tr>';
    html += '<th class="sticky-top sticky-col">Категория \\ Подразделение</th>';
    departments.forEach((dep) => {
        html += '<th class="sticky-top">' + String(dep.name).replace(/&/g, '&amp;').replace(/</g, '&lt;') + '</th>';
    });
    html += '</tr></thead>';
    html += '<tbody>';

    categories.forEach((cat) => {
        html += '<tr>';
        html += '<th class="sticky-col">' + String(cat.name).replace(/&/g, '&amp;').replace(/</g, '&lt;') + '</th>';
        departments.forEach((dep) => {
            const v = (counts[cat.id] && counts[cat.id][dep.id] != null) ? Number(counts[cat.id][dep.id]) : 0;
            html += '<td class="num">' + v + '</td>';
        });
        html += '</tr>';
    });
    html += '</tbody></table>';
    return html;
}

const btnPreview = document.getElementById('btn-preview-quantitative');
if (btnPreview) {
    btnPreview.addEventListener('click', async () => {
        const data = await fetchQuantitativeReportData();
        if (!data) return;
        if (!data.categories || data.categories.length === 0) {
            alert('Нет данных по выбранным параметрам.');
            return;
        }
        const wrap = document.getElementById('previewTableWrap');
        if (wrap) wrap.innerHTML = buildPreviewTable(data);
        showPreviewModal();
    });
}
</script>
