<?php

namespace Flexplorer;

/**
 * Flexplorer - Editor záznamu.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$id       = $oPage->getRequestValue('id');

$engine = new Flexplorer($evidence);

if (!is_null($id)) {
    $engine->loadFromFlexiBee(is_numeric($id) ? intval($id) : $id );
    $originalData = $engine->getData();
    $recordInfo   = $engine->__toString();
} else {
    $recordInfo   = _('New record');
    $originalData = null;
}

$oPage->container->addItem( new ui\RecordEditor($engine) );

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
