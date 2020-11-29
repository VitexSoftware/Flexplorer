<?php

namespace Flexplorer;

/**
 * Flexplorer - Download document in requested format.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2018 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$id = $oPage->getRequestValue('id');
$evidence = $oPage->getRequestValue('evidence');
$format = $oPage->getRequestValue('format');

$document = new \AbraFlexi\RO(is_numeric($id) ? intval($id) : $id,
        ['evidence' => $evidence]);

if (empty($evidence)) {
    die(_('Wrong call'));
} else {
    $documentBody = $document->getInFormat($format);
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename=' . $document->getEvidence() . '_' . $document . '.pdf');
    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . strlen($documentBody));
    echo $documentBody;
}