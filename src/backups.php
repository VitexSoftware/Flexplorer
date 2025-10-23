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
    $filename = \constant('BACKUP_DIRECTORY').'/'.basename($delete);

    if (file_exists($filename)) {
        if (unlink($filename)) {
            $oPage->addStatusMessage(
                sprintf(_('%s was deleted'), $delete),
                'success',
            );
        } else {
            $oPage->addStatusMessage(
                sprintf(_('%s was not deleted'), $filename),
                'warning',
            );
        }
    }
}

$oPage->addItem(new ui\PageTop(_('Backups')));

$oPage->addItem(new \Ease\TWB5\Panel(
    _('Backups'),
    'success',
    new ui\BackupsTool(\constant('BACKUP_DIRECTORY'), '.*\.winstrom-backup'),
));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
