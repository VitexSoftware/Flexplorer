<?php

namespace Flexplorer;

/**
 * Flexplorer - DataTables data source.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2020 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

header('Content-Type: application/json');

$class = $oPage->getRequestValue('class');

/**
 * @var Engine Data Source
 */
$engine = new $class(null, ['evidence' => ui\WebPage::getRequestValue('evidence')]);

unset($_REQUEST['class']);
unset($_REQUEST['_']);

$dataRaw = $engine->getColumnsFromAbraFlexi('*', $_REQUEST);

foreach ($dataRaw as $row => $columns) {
    $dataRaw[$row]['lastUpdate'] = $dataRaw[$row]['lastUpdate']->format(\AbraFlexi\RO::$DateTimeFormat);
}

echo json_encode(['data' => $dataRaw, 'recordsTotal' => count($dataRaw)]);

exit;

$evidence = $oPage->getRequestValue('evidence');
if (strlen($evidence)) {
    $datasource = new DataSource(new Flexplorer($evidence));
    $datasource->output();
} else {
    $stitek = $oPage->getRequestValue('stitek');
    if (strlen($stitek)) {
        $datasource = new DataSource(new SearchFlexplorer(['stitek' => $stitek]));
        $datasource->output();
    }
}
