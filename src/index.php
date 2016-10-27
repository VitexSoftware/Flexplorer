<?php

namespace Flexplorer;

/**
 * Flexplorer - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$oPage->addItem(new ui\PageTop(_('FlexiBee info')));

$statuser = new \FlexiPeeHP\Status();

$infoTable = new \Ease\Html\TableTag(null, ['class' => 'table']);

foreach ($statuser->getData() as $property => $value) {
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
