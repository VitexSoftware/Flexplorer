<?php

namespace Flexplorer;

/**
 * Flexplorer - Editor záznamu.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$id = $oPage->getRequestValue('id');

$engine = new Flexplorer($evidence);

if (!is_null($id)) {
    $engine->loadFromAbraFlexi(is_numeric($id) ? intval($id) : $id);
    $originalData = $engine->getData();
    $recordInfo = $engine->__toString();
} else {
    $recordInfo = _('New record');
    $originalData = null;
}

if ($oPage->isPosted()) {
    unset($_POST['url']);
    unset($_POST['body']);
    unset($_POST['class']);
    unset($_POST['method']);
    if (isset($_POST['deleteExtID']) && count($_POST['deleteExtID'])) {
        $extidRemove = $_POST['deleteExtID'];
        unset($_POST['deleteExtID']);
        $engine->setDataValue('@removeExternalIds', implode(',', $extidRemove));
    }

    $engine->takeData($_POST);

    if (!is_null($oPage->getRequestValue('toAbraFlexi'))) {
        if (isset($originalData['external-ids'])) {
            $engine->changeExternalIDs($originalData['external-ids']);
        }
        $engine->insertToAbraFlexi();
        if ($engine->lastResponseCode != 400) {
            $id = $engine->getLastInsertedId();
            $engine->addStatusMessage(_('Record was saved'), 'success');
        } else {
            $engine->addStatusMessage(_('Record was not saved'), 'warning');
        }
    }
}

$oPage->addItem(new ui\PageTop(_('Editor') . ' ' . $evidence . ' ' . $recordInfo));

if ($oPage->isPosted() && is_null($oPage->getRequestValue('toAbraFlexi'))) {
    $url = $engine->getEvidenceURL();

    $method = 'POST';
    $body = $engine->getJsonizedData($engine->getData());

    $oPage->container->addItem(new \Ease\TWB5\Panel(
        new \Ease\Html\H1Tag('<a href="evidence.php?evidence=' . $evidence . '">' . $evidence . '</a> <a href="editor.php?evidence=' . $evidence . '&id=' . $id . '">' . $recordInfo),
        'info',
        new ui\SendForm($url, $method, $body)
    ));
} else {
    $oPage->container->addItem(new \Ease\TWB5\Panel(
        new \Ease\Html\H1Tag('<a href="evidence.php?evidence=' . $evidence . '">' . $evidence . '</a> <a href="editor.php?evidence=' . $evidence . '&id=' . $id . '">' . $recordInfo . '</a>'),
        'info',
        new ui\Editor($engine)
    ));
}

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
