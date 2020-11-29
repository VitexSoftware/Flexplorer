<?php

/**
 * Flexplorer - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$statuser = new \AbraFlexi\Status();
$_SESSION['lasturl'] = $statuser->curlInfo['url'];

$oPage->addItem(new ui\PageTop(_('AbraFlexi info')));

$infoTable = new \Ease\Html\TableTag(null, ['class' => 'table']);

foreach ($statuser->getData() as $property => $value) {

    switch ($property) {
        case 'startupTime':
            $value = new \Ease\TWB\Widgets\LiveAge(\AbraFlexi\RO::flexiDateTimeToDateTime($value)->getTimestamp());
            break;
        case 'memoryHeap':
        case 'memoryUsed':
            $value = ui\WebPage::formatBytes($value);
            break;
    }

    $infoTable->addRowColumns([$property, $value]);
}


$infoRow = new \Ease\TWB\Row();
$infoRow->addColumn(6, new \Ease\TWB\Panel(_('server info'), 'info', $infoTable));
$infoRow->addColumn(6,
        new \Ease\TWB\Panel(_('license info'), 'info',
                new ui\LicenseInfo($_SESSION['license'])));
$oPage->container->addItem($infoRow);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
