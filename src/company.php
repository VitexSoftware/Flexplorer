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

$companyActions = new \Ease\TWB\Row();


$companyActions->addColumn(2,
    new \Ease\TWB\LinkButton('resetcompany.php',
        new \Ease\TWB\GlyphIcon('repeat').' '._('Reset'), 'danger',
        ['onClick' => "$('#Preloader').css('visibility', 'visible');", 'title' => _('Drop company and create again')]));

$companyActions->addColumn(1,
    new \Ease\TWB\LinkButton('copycompany.php',
        new \Ease\TWB\GlyphIcon('duplicate').' '._('Duplicate'), 'success',
        ['onClick' => "$('#Preloader').css('visibility', 'visible');", 'title' => _('Create copy')]));

$backupFile = '../backups/'.$company.'.winstrom-backup';

$companyActions->addColumn(2,
    new \Ease\TWB\LinkButton('restorecompany.php',
        new \Ease\TWB\GlyphIcon('floppy-open').' '._('Restore'), 'warning',
        ['onClick' => "$('#Preloader').css('visibility', 'visible');", 'title' => _('Restore previously saved state')]));


$companyActions->addColumn(1,
    new \Ease\TWB\LinkButton('savecompany.php',
        new \Ease\TWB\GlyphIcon('floppy-save').' '._('Save'), 'success',
        ['onClick' => "$('#Preloader').css('visibility', 'visible');", 'title' => _('Save current state')]));

$companyActions->addColumn(2,
    new \Ease\TWB\LinkButton('deletecompany.php',
        new \Ease\TWB\GlyphIcon('remove').' '._('Remove'), 'danger',
        ['onClick' => "$('#Preloader').css('visibility', 'visible');", 'title' => 'Drop all company data']));

$companyActions->addColumn(2,
    new \Ease\TWB\LinkButton('editor.php?evidence=nastaveni&company='.$company.'&id=1',
        new \Ease\TWB\GlyphIcon('wrench').' '._('Settings'), 'info',
        ['title' => 'Serveral company settings']));


$companyActions->addColumn(2,
    new \Ease\TWB\LinkButton('newcompany.php',
        new \Ease\TWB\GlyphIcon('plus').' '._('Create company'), 'success',
        ['title' => 'Create new company']));


$companyInfo = new \Ease\Html\PreTag(print_r($companer->getData(), true));

$companyPanel = new \Ease\TWB\Panel(new \Ease\Html\H2Tag($companer->getDataValue('nazev')),
    'info', $companyInfo, $companyActions);


$companyPanel->addItem(\FlexiPeeHP\FlexiBeeRO::flexiDateToDateTime($companer->getDataValue('createDt')));


//$companyPanel->addItem(new \Ease\Html\PreTag(print_r($settings, true)));


$oPage->container->addItem($companyPanel);

$oPage->addItem(new ui\PageBottom());

\Ease\Shared::webPage()->body->setTagClass('fuelux');
\Ease\Shared::webPage()->body->addItem(new ui\FXPreloader('Preloader'));


$oPage->draw();