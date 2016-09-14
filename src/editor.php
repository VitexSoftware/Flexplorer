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

if ($oPage->isPosted()) {
    $engine->takeData($_POST);

    if (!is_null($oPage->getRequestValue('toFlexiBee'))) {
        $engine->insertToFlexiBee();
        if ($engine->lastResponseCode != 400) {
            $id = $engine->getLastInsertedId();
            $engine->addStatusMessage(_('Record was saved'), 'success');
        } else {
            $engine->addStatusMessage(_('Record was not saved'), 'warning');
        }
    }
}

if (!is_null($id)) {
    $engine->loadFromFlexiBee($id);
    $recordInfo = $engine->__toString();
} else {
    $recordInfo = _('New record');
}

$oPage->addItem(new ui\PageTop(_('Editor')));



if ($oPage->isPosted() && is_null($oPage->getRequestValue('toFlexiBee'))) {

    $url = $engine->getEvidenceURL();

    $method = 'POST';
    $body   = $engine->jsonizeData($engine->getData());

    $oPage->container->addItem(new \Ease\TWB\Panel(new \Ease\Html\H1Tag('<a href="evidence.php?evidence='.$evidence.'">'.$evidence.'</a> '.$recordInfo),
        'info', new ui\SendForm($url, $method, $body)));
} else {
    $oPage->container->addItem(new \Ease\TWB\Panel(new \Ease\Html\H1Tag('<a href="evidence.php?evidence='.$evidence.'">'.$evidence.'</a> '.$recordInfo),
        'info', new ui\Editor($engine)));
}

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
