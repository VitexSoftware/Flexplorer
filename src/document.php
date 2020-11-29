<?php

namespace Flexplorer;

/**
 * Flexplorer - Document View.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$id = $oPage->getRequestValue('id');

if (strstr($id, ',')) {
    $ids = explode(',', $id);
}

$evidence = $oPage->getRequestValue('evidence');
$embed = $oPage->getRequestValue('embed');
$report = $oPage->getRequestValue('report-name');

if (empty($embed)) {
    $oPage->addItem(new ui\PageTop($evidence . ' #' . $id));
} else {
    $oPage = new \Ease\WebPage($evidence . ' #' . $id);
}

if (isset($ids)) {
    $document = new \AbraFlexi\RO(null, ['evidence' => $evidence]);
} else {
    $document = new \AbraFlexi\RO(is_numeric($id) ? intval($id) : $id,
            ['evidence' => $evidence, 'list' => 'id']);
}

if (empty($embed)) {
    $oPage->addItem(new ui\PageTop($document->getEvidence() . ' ' . $document));
}
$embeded = new \AbraFlexi\ui\EmbedResponsivePDF($document, 'getpdf.php', $report);

if (empty($embed)) {
    $oPage->container->addItem($embeded);
    $oPage->addItem(new ui\PageBottom());
} else {
    $oPage->addItem($embeded);
}

$oPage->draw();
