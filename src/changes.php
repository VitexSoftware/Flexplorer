<?php

/**
 * Flexplorer - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$delete = $oPage->getRequestValue('delete');

$oPage->onlyForLogged();

$chages = [];

$d = dir(HookReciever::getSaveDir());
while (false !== ($entry = $d->read())) {
    if (strstr($entry, 'flexplorer-change')) {
        if ($delete === 'all') {
            unlink(HookReciever::getSaveDir() . '/' . $entry);
        } else {
            $chages[$entry] = $entry;
        }
    }
}
$d->close();

arsort($chages);

$oPage->addItem(new ui\PageTop(_('AbraFlexi WebHook income')));

$oPage->container->addItem(new ui\ChangesLister($chages));

if (count($chages)) {
    $oPage->container->addItem(new \Ease\TWB5\LinkButton('?delete=all',
                    _('Delete All'), 'danger'));
} else {

    $webHookUrl = str_replace(basename(__FILE__), 'webhook.php', \Ease\Document::phpSelf());

    $oPage->container->addItem(new \Ease\TWB5\LinkButton("changesapi.php?hookurl=" . urlencode($webHookUrl),
                    _('Target to FlexPlorer'), 'success',
                    ['class' => 'button button-xs']));

    $oPage->addStatusMessage('WebHook not triggered yet');
}
$oPage->addItem(new ui\PageBottom());

$oPage->draw();
