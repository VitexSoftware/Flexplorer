<?php

namespace Flexplorer;

/**
 * Flexplorer - Mazání záznamu.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$id = $oPage->getRequestValue('id');

if (is_null($evidence)) {
    $oPage->redirect('index.php');
}

if (is_null($id)) {
    $oPage->redirect('evidence.php?evidence=' . $evidence);
}

$engine = new Flexplorer($evidence);

$delete = $oPage->getGetValue('delete', 'bool');
if ($delete === true) {
    if ($engine->deleteFromAbraFlexi($id)) {
        $engine->addStatusMessage(_('Record was deleted'), 'success');
        $oPage->redirect('evidence.php?evidence=' . $evidence);
    } else {
        $engine->addStatusMessage(_('Record was not deleted'), 'warning');
    }
} else {
    $engine->loadFromAbraFlexi($id);
    $recordInfo = $engine->__toString();
}

$oPage->addItem(new ui\PageTop(_('Record Delete')));

$buttonRow = new \Ease\TWB5\Row();
$buttonRow->addColumn(4);
$buttonRow->addColumn(4,
        new \Ease\TWB5\LinkButton('evidence.php?evidence=' . $evidence,
                _('Keep record') . ' ' . new \Ease\TWB5\GlyphIcon('ok-sign'), 'info',
                ['class' => 'btn btn-default clearfix pull-right']));
$buttonRow->addColumn(4,
        new \Ease\TWB5\LinkButton('delete.php?evidence=' . $evidence . '&delete=true&id=' . $id,
                _('Delete record') . ' ' . new \Ease\TWB5\GlyphIcon('remove-sign'), 'danger'));

$deleteTabs = new \Ease\TWB5\Tabs('DeleteTabs');
$deleteTabs->addTab(_('Overview'), new ui\RecordShow($engine, $buttonRow));
$deleteTabs->addTab(_('AbraFlexi'),
        new \Ease\Html\IframeTag(str_replace('.json', '.html',
                        $engine->getEvidenceURL() . '/' . $engine->getMyKey() . '.' . $engine->format . '?inDesktopApp=true'),
                ['style' => 'width: 100%; height: 600px', 'frameborder' => 0]));

$oPage->container->addItem($deleteTabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
