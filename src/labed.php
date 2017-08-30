<?php
/**
 * Flexplorer - Label Assigment Change saver.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$id       = $oPage->getRequestValue('record');
$label    = $oPage->getRequestValue('label');
$evidence = $oPage->getRequestValue('evidence');
$result   = false;

if ($id && $label && $evidence) {
    $flexiBee = new \FlexiPeeHP\FlexiBeeRW(['id' => $id],
        ['evidence' => $evidence]);

    if ($oPage->getRequestValue('state', 'boolean')) {
        $result = \FlexiPeeHP\Stitek::setLabel($label, $flexiBee);
    } else {
        $urlparbac                            = $flexiBee->defaultUrlParams;
        $flexiBee->defaultUrlParams['detail'] = 'custom:id,stitky';
        $flexiBee->loadFromFlexiBee(is_numeric($id) ? intval($id) : $id);
        $result                               = \FlexiPeeHP\Stitek::unsetLabel($label,
                $flexiBee);
        $flexiBee->defaultUrlParams           = $urlparbac;
    }
    http_response_code($flexiBee->lastResponseCode);
} else {
    http_response_code(404);
}



