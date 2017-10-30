<?php

namespace Flexplorer;

/**
 * Flexplorer - Create new company.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';


$oPage->onlyForLogged();

$company = $_SESSION['company'];

$loader = new \FlexiPeeHP\Company(['dbNazev' => $company]);
if ($loader->deleteFromFlexiBee()) {
    $loader->addStatusMessage(_('company removed before restore'), 'warning');
} else {
    $loader->addStatusMessage(_('company cleanup failed'), 'warning');
}

if ($loader->restoreBackupFrom(constant('BACKUP_DIRECTORY').$company.'.winstrom-backup')) {
    $loader->addStatusMessage(_('backup restored'), 'success');
} else {
    $loader->addStatusMessage(sprintf(_('company %s was not restored'), $company),
        'warning');
}

$oPage->redirect('index.php');

