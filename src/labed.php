<?php

/**
 * Flexplorer - Label Assigment Change saver.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$id = $oPage->getRequestValue('record');
$label = $oPage->getRequestValue('label');
$evidence = $oPage->getRequestValue('evidence');
$result = false;

if ($id && $label && $evidence) {
    $abraFlexi = new \AbraFlexi\RW(
        ['id' => $id],
        ['evidence' => $evidence]
    );

    if ($oPage->getRequestValue('state', 'boolean')) {
        $result = \AbraFlexi\Stitek::setLabel($label, $abraFlexi);
    } else {
        $urlparbac = $abraFlexi->defaultUrlParams;
        $abraFlexi->defaultUrlParams['detail'] = 'custom:id,stitky';
        $abraFlexi->loadFromAbraFlexi(is_numeric($id) ? intval($id) : $id);
        $result = \AbraFlexi\Stitek::unsetLabel(
            $label,
            $abraFlexi
        );
        $abraFlexi->defaultUrlParams = $urlparbac;
    }
    http_response_code($abraFlexi->lastResponseCode);
} else {
    http_response_code(404);
}
