<?php

/**
 * Flexplorer - Create company clone.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';


$oPage->onlyForLogged();

$name = $oPage->getRequestValue('nazev');

$companer = new \AbraFlexi\Company();

$company = $_SESSION['company'];

if (empty($name)) {
    $oPage->addItem(new ui\PageTop(_('Clone company')));

    $companyNewIndex = intval(preg_replace('/[^0-9]+/', '', $company), 10) + 1;

    $newCompany = preg_replace('/[0-9]+$/', '', $company);

    $companer->setDataValue('nazev', $newCompany . $companyNewIndex);
    $oPage->container->addItem(new \Ease\TWB\Panel(_('Clone to'), 'success',
                    new ui\CompanyForm($companer)));

    $oPage->addItem(new ui\PageBottom());
} else {
    $cloner = new \AbraFlexi\Company(['dbNazev' => $company]);
    if ($cloner->saveBackupTo(constant('BACKUP_DIRECTORY') . $company . '.winstrom-backup')) {
        $cloner->addStatusMessage(_('backup saved'), 'success');

        $cloner->setDataValue('dbNazev', $name);
        if ($cloner->restoreBackupFrom(constant('BACKUP_DIRECTORY') . $company . '.winstrom-backup')) {
            $cloner->addStatusMessage(_('backup restored'), 'success');
            $oPage->redirect('company.php?company=' . $name);
        } else {
            $cloner->addStatusMessage(sprintf(_('company %s was not restored'),
                            $company), 'warning');
            $oPage->redirect('company.php?company=' . $company);
        }
    } else {
        $cloner->addStatusMessage(sprintf(_('Saving company %s failed'),
                        $company), 'error');

        $oPage->redirect('company.php?company=' . $company);
    }
}


$oPage->draw();
