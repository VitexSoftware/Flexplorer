<?php

namespace Flexplorer;

/**
 * Flexplorer - DataTables data source.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2024 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

header('Content-Type: application/json');

$class = $oPage->getRequestValue('class');

/**
 * @var Engine Data Source
 */
$engine = new $class(ui\WebPage::getRequestValue('evidence'));

unset($_REQUEST['class']);
unset($_REQUEST['_']);
unset($_REQUEST['XDEBUG_SESSION_START']);

$dataRaw = $engine->getColumnsFromAbraFlexi('*', array_merge($_REQUEST, ['add-row-count' => true]));

foreach ($dataRaw as $row => $columns) {
    $dataRaw[$row]['lastUpdate'] = (array_key_exists('lastUpdate', $dataRaw) && $dataRaw[$row]['lastUpdate']) ? $dataRaw[$row]['lastUpdate']->format(\AbraFlexi\DateTime::$format) : '';
    foreach ($columns as $column => $value) {
        $dataRaw[$row][$column] = strval($dataRaw[$row][$column]);
    }
}

echo json_encode(['recordsTotal' => $engine->rowCount, 'recordsFiltered' => $engine->rowCount, 'data' => $dataRaw]);

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
