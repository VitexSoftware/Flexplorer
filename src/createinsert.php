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

if (null !== $id) {
    $engine->loadFromAbraFlexi(is_numeric($id) ? (int) $id : $id);
    $originalData = $engine->getData();
    $recordInfo = $engine->__toString();
} else {
    $recordInfo = _('New record');
    $originalData = null;
}

if ($oPage->isPosted()) {
    unset($_POST['url'], $_POST['body'], $_POST['class'], $_POST['method']);

    if (isset($_POST['deleteExtID']) && \count($_POST['deleteExtID'])) {
        $extidRemove = $_POST['deleteExtID'];
        unset($_POST['deleteExtID']);
        $engine->setDataValue('@removeExternalIds', implode(',', $extidRemove));
    }

    $engine->takeData($_POST);

    if (null !== $oPage->getRequestValue('toAbraFlexi')) {
        if (isset($originalData['external-ids'])) {
            $engine->changeExternalIDs($originalData['external-ids']);
        }

        $engine->insertToAbraFlexi();

        if ($engine->lastResponseCode !== 400) {
            $id = $engine->getLastInsertedId();
            $engine->addStatusMessage(_('Record was saved'), 'success');
        } else {
            $engine->addStatusMessage(_('Record was not saved'), 'warning');
        }
    }
}

$oPage->addItem(new ui\PageTop(_('Editor').' '.$evidence.' '.$recordInfo));

if ($oPage->isPosted() && null === $oPage->getRequestValue('toAbraFlexi')) {
    $url = $engine->getEvidenceURL();

    $method = 'POST';
    $body = $engine->getJsonizedData($engine->getData());

    $oPage->addItem(new \Ease\TWB5\Panel(
        new \Ease\Html\H1Tag('<a href="evidence.php?evidence='.$evidence.'">'.$evidence.'</a> <a href="editor.php?evidence='.$evidence.'&id='.$id.'">'.$recordInfo),
        'info',
        new ui\SendForm($url, $method, $body),
    ));
} else {
    $oPage->addItem(new \Ease\TWB5\Panel(
        new \Ease\Html\H1Tag('<a href="evidence.php?evidence='.$evidence.'">'.$evidence.'</a> <a href="editor.php?evidence='.$evidence.'&id='.$id.'">'.$recordInfo.'</a>'),
        'info',
        new ui\Editor($engine),
    ));
}

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
