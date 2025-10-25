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
$evidence = $oPage->getRequestValue('evidence');
$embed = $oPage->getRequestValue('embed');
$report = $oPage->getRequestValue('report-name');

if (strstr((string)$id, ',')) {
    $ids = explode(',', $id);
}

// For list reports (no ID), create document without specific record
if (empty($id)) {
    $document = new \AbraFlexi\RO(null, ['evidence' => $evidence]);
} elseif (isset($ids)) {
    $document = new \AbraFlexi\RO(null, ['evidence' => $evidence]);
} else {
    $document = new \AbraFlexi\RO(
        is_numeric($id) ? (int) $id : $id,
        ['evidence' => $evidence, 'list' => 'id'],
    );
}

// For AJAX/embed mode, output only the iframe HTML
if ($embed === 'true') {
    // Build URL for getpdf.php
    $params = ['evidence' => $evidence, 'embed' => 'true'];
    if (!empty($id)) {
        $params['id'] = $id;
    }
    if (!empty($report)) {
        $params['report-name'] = $report;
    }
    $pdfUrl = 'getpdf.php?' . http_build_query($params);
    
    // Output only the iframe, no page wrapper
    echo '<div style="width:100%; height:80vh;">';
    echo '<iframe src="' . htmlspecialchars($pdfUrl) . '" ';
    echo 'style="width:100%; height:100%; border:none;" ';
    echo 'type="application/pdf"></iframe>';
    echo '</div>';
    exit;
}

// For non-embed mode, render full page
$pageTitle = empty($id) ? $evidence : $evidence.' #'.$id;
$oPage->addItem(new ui\PageTop($pageTitle));
$oPage->addItem(new ui\PageTop($document->getEvidence().' '.$document));

$embeded = new ui\EmbedResponsivePDF($document, 'getpdf.php', $report);
$oPage->addItem($embeded);
$oPage->addItem(new ui\PageBottom());
$oPage->draw();
