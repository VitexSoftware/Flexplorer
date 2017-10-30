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

$saver = new \FlexiPeeHP\Company(['dbNazev' => $company]);
if ($saver->saveBackupTo(constant('BACKUP_DIRECTORY').$company.'.winstrom-backup')) {
    $saver->addStatusMessage(_('backup saved'), 'success');
}


$oPage->redirect('company.php?company='.$company);

