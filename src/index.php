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

$oPage->onlyForLogged();

$statuser = new \AbraFlexi\Status();
$_SESSION['lasturl'] = $statuser->curlInfo['url'];

$oPage->addToHeader(new ui\PageTop(_('AbraFlexi info')));

$infoTable = new \Ease\Html\TableTag(null, ['class' => 'table']);

foreach ($statuser->getData() as $property => $value) {
    switch ($property) {
        case 'startupTime':
            $value = new \Ease\Html\Widgets\LiveAge(\AbraFlexi\RO::flexiDateTimeToDateTime($value));

            break;
        case 'memoryHeap':
        case 'memoryUsed':
            $value = \Ease\Functions::formatBytes($value);

            break;
    }

    $infoTable->addRowColumns([$property, $value]);
}


$infoRow = new \Ease\TWB5\Row();
$infoRow->addColumn(6, new \Ease\TWB5\Panel(_('server info'), 'default', $infoTable));
$infoRow->addColumn(6, new \Ease\TWB5\Panel(_('license info'), 'default', new ui\LicenseInfo($_SESSION['license'])));
$oPage->addToMain($infoRow);

$oPage->addToFooter(new ui\PageBottom());

$oPage->draw();
