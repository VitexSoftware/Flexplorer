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
$embed    = $oPage->getRequestValue('embed');

if (empty($embed)) {
    $oPage->addItem(new ui\PageTop($evidence.' #'.$id));
} else {
    $oPage = new \Ease\WebPage($evidence.' #'.$id);
}

if (isset($ids)) {
    $document = new \FlexiPeeHP\FlexiBeeRO(null, ['evidence' => $evidence]);
} else {
    $document = new \FlexiPeeHP\FlexiBeeRO(is_numeric($id) ? intval($id) : $id,
        ['evidence' => $evidence]);
}

if (empty($embed)) {
    $oPage->addItem(new ui\PageTop($document->getEvidence().' '.$document));
}
$embeded = new \FlexiPeeHP\ui\EmbedResponsivePDF($document);

if (empty($embed)) {
    $oPage->container->addItem($embeded);
    $oPage->addItem(new ui\PageBottom());
} else {
    $oPage->addItem($embeded);
}

$oPage->draw();
