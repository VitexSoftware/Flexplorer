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

$backup  = $oPage->getRequestValue('backup');
$company = $_SESSION['company'];
$loader  = new \FlexiPeeHP\Company(['dbNazev' => $company]);


if (empty($backup)) {
    $oPage->addItem(new ui\PageTop(_('Baskup restore')));
    $oPage->container->addItem('Specify backup to restore');

    constant('BACKUP_DIRECTORY').$company.'.winstrom-backup';
    $d     = dir(constant('BACKUP_DIRECTORY'));
    echo "Handle: ".$d->handle."\n";
    echo "Path: ".$d->path."\n";
    while (false !== ($entry = $d->read())) {
        $oPage->container->addItem($entry);
    }
    $d->close();

    $oPage->addItem(new ui\PageBottom());

    $oPage->draw();
} else {

    if ($loader->deleteFromFlexiBee()) {
        $loader->addStatusMessage(_('company removed before restore'), 'warning');
    } else {
        $loader->addStatusMessage(_('company cleanup failed'), 'warning');
    }

    if ($loader->restoreBackupFrom(constant('BACKUP_DIRECTORY').$company.'.winstrom-backup')) {
        $loader->addStatusMessage(_('backup restored'), 'success');
    } else {
        $loader->addStatusMessage(sprintf(_('company %s was not restored'),
                $company), 'warning');
    }

    $oPage->redirect('company.php?company='.$company);
}

