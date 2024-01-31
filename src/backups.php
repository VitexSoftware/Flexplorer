<?php

namespace Flexplorer;

/**
 * Flexplorer - Backups Listing.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$delete = $oPage->getRequestValue('delete');
if (!empty($delete)) {
    $filename = constant('BACKUP_DIRECTORY') . '/' . basename($delete);
    if (file_exists($filename)) {
        if (unlink($filename)) {
            $oPage->addStatusMessage(
                sprintf(_('%s was deleted'), $delete),
                'success'
            );
        } else {
            $oPage->addStatusMessage(
                sprintf(_('%s was not deleted'), $filename),
                'warning'
            );
        }
    }
}

$oPage->addItem(new ui\PageTop(_('Backups')));

$oPage->container->addItem(new \Ease\TWB5\Panel(
    _('Backups'),
    'success',
    new ui\BackupsTool(constant('BACKUP_DIRECTORY'), '.*\.winstrom-backup')
));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
