<?php

namespace Flexplorer;

/**
 * Flexplorer - Editor záznamu.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2018 Vitex Software
 */

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getGetValue('evidence');
$id = $oPage->getRequestValue('id');

$engine = new Flexplorer($evidence);

if (empty($id)) {
    if (\Ease\Page::isPosted()) {
        unset($_POST['id']);
        $engine->takeData($_POST);
        $oPage->addStatusMessage(
            _('New record save'),
            $engine->sync() ? 'success' : 'error'
        );
        $id = $engine->getRecordID();
    } else {
        $recordInfo = _('New record');
        $originalData = null;
    }
} else {
    $engine->loadFromAbraFlexi(is_numeric($id) ? intval($id) : $id);
    $originalData = $engine->getData();
    $recordInfo = $engine->__toString();
}

$oPage->addItem(new ui\PageTop(_('Record Editor') . ' ' . $evidence . ':' . $id));

$editorTabs = new \Ease\TWB5\Tabs('EditorTabs');
$editorTabs->addTab(_('Record Editor'), new ui\RecordEditor($engine));

$editorTabs->addTab(
    _('External IDs'),
    new \Ease\TWB5\Form(
        ['name' => 'extIDs', 'action' => 'createinsert.php', 'method' => 'POST'],
        new ui\extIDsEditor($engine)
    )
);
$editorTabs->addTab(_('Labels'), new ui\LabelSwitches($engine));

$editorTabs->addAjaxTab(
    _('PDF'),
    'document.php?embed=true&evidence=' . $evidence . '&id=' . $engine->getMyKey()
);
$editorTabs->addTab(_('Print Sets'), new ui\PrintSetGallery($engine));
$editorTabs->addTab(_('Downloads'), new ui\RecordDownloader($engine));

$oPage->container->addItem($editorTabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
