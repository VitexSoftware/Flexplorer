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

require_once 'includes/Init.php';

$delete = $oPage->getRequestValue('delete');

$oPage->onlyForLogged();

$chages = [];

$d = dir(HookReciever::getSaveDir());

while (false !== ($entry = $d->read())) {
    if (strstr($entry, 'flexplorer-change')) {
        if ($delete === 'all') {
            unlink(HookReciever::getSaveDir().'/'.$entry);
        } else {
            $chages[$entry] = $entry;
        }
    }
}

$d->close();

arsort($chages);

$oPage->addItem(new ui\PageTop(_('AbraFlexi WebHook income')));

$oPage->addItem(new ui\ChangesLister($chages));

if (\count($chages)) {
    $oPage->addItem(new \Ease\TWB5\LinkButton(
        '?delete=all',
        _('Delete All'),
        'danger',
    ));
} else {
    $webHookUrl = str_replace(basename(__FILE__), 'webhook.php', \Ease\Document::phpSelf());

    $oPage->addItem(new \Ease\TWB5\LinkButton(
        'changesapi.php?hookurl='.urlencode($webHookUrl),
        _('Target to FlexPlorer'),
        'success',
        ['class' => 'button button-xs'],
    ));

    $oPage->addStatusMessage('WebHook not triggered yet');
}

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
