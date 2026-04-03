<?php
/** @var app\models\Department[] $departments */
/** @var app\models\Category[] $categories */
/** @var array $counts */

function xlsXmlEscape($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_XML1, 'UTF-8');
}
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:html="http://www.w3.org/TR/REC-html40">
    <Styles>
        <Style ss:ID="Header">
            <Font ss:Bold="1"/>
            <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            <Interior ss:Color="#E9EEF5" ss:Pattern="Solid"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
        </Style>
        <Style ss:ID="RowHeader">
            <Font ss:Bold="1"/>
            <Interior ss:Color="#F8F9FB" ss:Pattern="Solid"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
        </Style>
        <Style ss:ID="CellNum">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
        </Style>
    </Styles>
    <Worksheet ss:Name="Количественный отчет">
        <Table>
            <Column ss:AutoFitWidth="0" ss:Width="260"/>
            <?php foreach ($departments as $dep): ?>
                <Column ss:AutoFitWidth="0" ss:Width="140"/>
            <?php endforeach; ?>
            <Row>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Категория \ Подразделение</Data></Cell>
                <?php foreach ($departments as $dep): ?>
                    <Cell ss:StyleID="Header"><Data ss:Type="String"><?= xlsXmlEscape($dep->name) ?></Data></Cell>
                <?php endforeach; ?>
            </Row>
            <?php foreach ($categories as $cat): ?>
                <Row>
                    <Cell ss:StyleID="RowHeader"><Data ss:Type="String"><?= xlsXmlEscape($cat->name) ?></Data></Cell>
                    <?php foreach ($departments as $dep): ?>
                        <Cell ss:StyleID="CellNum"><Data ss:Type="Number"><?= (int)($counts[$cat->id][$dep->id] ?? 0) ?></Data></Cell>
                    <?php endforeach; ?>
                </Row>
            <?php endforeach; ?>
        </Table>
        <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
            <FreezePanes/>
            <FrozenNoSplit/>
            <SplitHorizontal>1</SplitHorizontal>
            <TopRowBottomPane>1</TopRowBottomPane>
            <SplitVertical>1</SplitVertical>
            <LeftColumnRightPane>1</LeftColumnRightPane>
            <Panes>
                <Pane>
                    <Number>3</Number>
                    <ActiveRow>1</ActiveRow>
                    <ActiveCol>1</ActiveCol>
                </Pane>
            </Panes>
            <ProtectObjects>False</ProtectObjects>
            <ProtectScenarios>False</ProtectScenarios>
        </WorksheetOptions>
    </Worksheet>
</Workbook>
<?php
/** @var app\models\Department[] $departments */
/** @var app\models\Category[] $categories */
/** @var array $counts */
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; }
        th, td { border: 1px solid #444; padding: 6px 8px; }
        th { background: #e9eef5; }
        .cat { font-weight: bold; background: #f8f9fb; }
        .num { text-align: center; }
    </style>
</head>
<body>
<table>
    <thead>
    <tr>
        <th>Категория \ Подразделение</th>
        <?php foreach ($departments as $dep): ?>
            <th><?= htmlspecialchars($dep->name) ?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($categories as $cat): ?>
        <tr>
            <td class="cat"><?= htmlspecialchars($cat->name) ?></td>
            <?php foreach ($departments as $dep): ?>
                <td class="num"><?= (int)($counts[$cat->id][$dep->id] ?? 0) ?></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
