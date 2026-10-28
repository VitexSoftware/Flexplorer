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

/**
 * Flexplorer - Company Page.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

use Flexplorer\ui\WebPage;

require_once 'includes/Init.php';
require_once 'includes/csrf.php';

$oPage->onlyForLogged();

$company = $oPage->getRequestValue('company');

if (empty($company)) {
    $oPage->redirect('companies.php');
} else {
    $setinger = new \AbraFlexi\Nastaveni();

    try {
        $settings = $setinger->getColumnsFromAbraFlexi('*', ['nazFirmy' => $company, 'detail' => 'full']);
    } catch (\AbraFlexi\Exception $exc) {
        $setinger->addStatusMessage($exc->getMessage());
    }

    $companer = new \AbraFlexi\Company($company);

    $oPage->setRequestURL($companer->apiURL);

    $oPage->addItem(new ui\PageTop($companer->getDataValue('nazev')));

    $companyActions = new \Ease\TWB5\Row();

    $companyActions->addColumn(
        2,
        csrfButton('resetcompany.php', '♻️ '._('Reset'), 'danger', _('Drop company and create again')),
    );

    $companyActions->addColumn(
        1,
        new \Ease\TWB5\LinkButton(
            'copycompany.php',
            '🖇️ '._('Duplicate'),
            'success',
            ['onClick' => "$('#Preloader').css('visibility', 'visible');", 'title' => _('Create copy')],
        ),
    );

    $backupFile = '../backups/'.$company.'.winstrom-backup';

    $companyActions->addColumn(
        2,
        new \Ease\TWB5\LinkButton(
            'restorecompany.php',
            '📤 '._('Restore'),
            'warning',
            ['onClick' => "$('#Preloader').css('visibility', 'visible');", 'title' => _('Restore previously saved state')],
        ),
    );

    $companyActions->addColumn(
        1,
        new \Ease\TWB5\LinkButton(
            'savecompany.php',
            '📥 '._('Save'),
            'success',
            ['title' => _('Save current state')],
        ),
    );

    $companyActions->addColumn(
        2,
        csrfButton('deletecompany.php', '🪦 '._('Remove'), 'danger', _('Drop all company data')),
    );

    $companyActions->addColumn(
        2,
        new \Ease\TWB5\LinkButton(
            'editor.php?evidence=nastaveni&company='.$company.'&id=1',
            '🛠️ '._('Settings'),
            'info',
            ['title' => 'Serveral company settings'],
        ),
    );

    $companyActions->addColumn(
        2,
        new \Ease\TWB5\LinkButton(
            'newcompany.php',
            '⛑️ '._('Create company'),
            'success',
            ['title' => 'Create new company'],
        ),
    );

    $companyInfo = new \Ease\Html\TableTag(null, ['class' => 'table']);

    $companyInfo->addRowColumns([_('database'), new ui\CopyToClipBoard(new \Ease\Html\InputTextTag(
        'dbNazev',
        $companer->getDataValue('dbNazev'),
        ['id' => 'dbNazev', 'readonly'],
    ))]);

    $created = \AbraFlexi\Functions::flexiDateTimeToDateTime($companer->getDataValue('createDt'));
    $companyInfo->addRowColumns([_('created'), \AbraFlexi\Functions::flexiDateTimeToDateTime($companer->getDataValue('createDt'))->format('d.m. Y').' ('._('before').' '.new \Ease\Html\Widgets\LiveAge($created).')']);

    $companyInfo->addRowColumns([_('Watching changes'), new ui\WatchingChangesStatus($companer->getDataValue('watchingChanges') === 'true')]);

    $companyInfo->addRowColumns([_('Show in listing'), new ui\BooleanLabel($companer->getDataValue('watchingChanges') === 'true')]);

    $companyInfo->addRowColumns([_('License Group'), $companer->getDataValue('licenseGroup')]);

    $companyInfo->addRowColumns([_('Status'), new \Ease\TWB5\Badge(
        $companer->getDataValue('stavEnum'),
        $companer->getDataValue('stavEnum') === 'ESTABLISHED' ? 'success' : 'warning',
    )]);

    $companyPanel = new \Ease\TWB5\Panel(
        new \Ease\Html\H2Tag($companer->getDataValue('nazev')),
        'inverse',
        $companyInfo,
        $companyActions,
    );

    $oPage->addItem($companyPanel);

    $oPage->addItem(new ui\PageBottom());

    WebPage::singleton()->body->setTagClass('fuelux');
    WebPage::singleton()->body->addItem(new ui\FXPreloader('Preloader'));

    $oPage->draw();
}
