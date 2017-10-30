<?php

namespace Flexplorer;

/**
 * Flexplorer - Company Page.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$company = $oPage->getRequestValue('company');

$setinger = new \FlexiPeeHP\Nastaveni();
$settings = $setinger->getColumnsFromFlexibee('*',
    ['nazFirmy' => $company, 'detail' => 'full']);

$companer = new \FlexiPeeHP\Company($company);

$oPage->addItem(new ui\PageTop($companer->getDataValue('nazev')));

$companyPanel = new \Ease\TWB\Panel(new \Ease\Html\H2Tag($companer->getDataValue('nazev')),
    'info');

//$companyPanel->addItem(new \Ease\Html\PreTag(print_r($companer->getData(), true)));
//$companyPanel->addItem(new \Ease\Html\PreTag(print_r($settings, true)));







$companyRow = new \Ease\TWB\Row();


$companyActions = new \Ease\TWB\Well();


$companyActions->addItem(new \Ease\TWB\LinkButton('newcompany.php',
        new \Ease\TWB\GlyphIcon('plus').' '._('New'), 'success'));

$companyActions->addItem(new \Ease\TWB\LinkButton('resetcompany.php',
        new \Ease\TWB\GlyphIcon('repeat').' '._('Reset'), 'danger',
        ['onClick' => "$('#Preloader').css('visibility', 'visible');"]));


$backupFile = '../backups/'.$company.'.winstrom-backup';
if (file_exists($backupFile)) {
    $companyActions->addItem(new \Ease\TWB\LinkButton('restorecompany.php',
            new \Ease\TWB\GlyphIcon('floppy-open').' '._('Restore'), 'warning',
            ['onClick' => "$('#Preloader').css('visibility', 'visible');"]));
}


$companyActions->addItem(new \Ease\TWB\LinkButton('savecompany.php',
        new \Ease\TWB\GlyphIcon('floppy-save').' '._('Save'), 'success',
        ['onClick' => "$('#Preloader').css('visibility', 'visible');"]));

$companyActions->addItem(new \Ease\TWB\LinkButton('deletecompany.php',
        new \Ease\TWB\GlyphIcon('remove').' '._('Remove'), 'danger',
        ['onClick' => "$('#Preloader').css('visibility', 'visible');"]));


$companyRow->addColumn(2, $companyActions);
$companyRow->addColumn(10, $companyPanel);

$oPage->container->addItem($companyRow);

$oPage->addItem(new ui\PageBottom());

\Ease\Shared::webPage()->body->setTagClass('fuelux');
\Ease\Shared::webPage()->body->addItem(new ui\FXPreloader('Preloader'));


$oPage->draw();
