<?php

namespace Flexplorer;

/**
 * Flexplorer - Mazání záznamu.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$id       = $oPage->getRequestValue('id');

if (is_null($evidence)) {
    $oPage->redirect('index.php');
}

if (is_null($id)) {
    $oPage->redirect('evidence.php?evidence='.$evidence);
}

$engine = new Flexplorer($evidence);


$delete = $oPage->getGetValue('delete', 'bool');
if ($delete == true) {
    if ($engine->deleteFromFlexiBee($id)) {
        $engine->addStatusMessage(_('Record was deleted'), 'success');
        $oPage->redirect('evidence.php?evidence='.$evidence);
    } else {
        $engine->addStatusMessage(_('Record was not deleted'), 'warning');
    }
} else {
    $engine->loadFromFlexiBee($id);
    $recordInfo = $engine->__toString();
}

$oPage->addItem(new ui\PageTop(_('Record Delete')));

$buttonRow = new \Ease\TWB\Row();
$buttonRow->addColumn(4);
$buttonRow->addColumn(4,
    new \Ease\TWB\LinkButton('evidence.php?evidence='.$evidence,
    _('Keep record').' '.new \Ease\TWB\GlyphIcon('ok-sign'), 'info',
    ['class' => 'btn btn-default clearfix pull-right']));
$buttonRow->addColumn(4,
    new \Ease\TWB\LinkButton('delete.php?evidence='.$evidence.'&delete=true&id='.$id,
    _('Delete record').' '.new \Ease\TWB\GlyphIcon('remove-sign'), 'danger'));

$oPage->container->addItem(new ui\RecordShow($engine, $buttonRow));


$oPage->addItem(new ui\PageBottom());

$oPage->draw();
