<?php

declare(strict_types=1);

/**
 * This file is part of the Flexplorer package
 *
 * github.com/VitexSoftware/Flexplorer
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flexplorer;

/**
 * Flexplorer - Document View.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
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
    $oPage->addItem(new ui\PageTop($evidence.' #'.$id));
} else {
    $oPage = new \Ease\WebPage($evidence.' #'.$id);
}

if (isset($ids)) {
    $document = new \AbraFlexi\RO(null, ['evidence' => $evidence]);
} else {
    $document = new \AbraFlexi\RO(
        is_numeric($id) ? (int) $id : $id,
        ['evidence' => $evidence, 'list' => 'id'],
    );
}

if (empty($embed)) {
    $oPage->addItem(new ui\PageTop($document->getEvidence().' '.$document));
}

$embeded = new \AbraFlexi\ui\EmbedResponsivePDF($document, 'getpdf.php', $report);

if (empty($embed)) {
    $oPage->container->addItem($embeded);
    $oPage->addItem(new ui\PageBottom());
} else {
    $oPage->addItem($embeded);
}

$oPage->draw();
