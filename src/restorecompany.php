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
 * Flexplorer - Create new company.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$backup = $oPage->getRequestValue('backup');
$company = $_SESSION['company'];
$loader = new \AbraFlexi\Company(['dbNazev' => $company]);

if (empty($backup)) {
    $oPage->addItem(new ui\PageTop(_('Backup restore')));
    $oPage->addStatusMessage('Specify backup to restore', 'warning');

    $oPage->addItem(new \Ease\TWB5\Panel(
        _('Backups'),
        'success',
        new ui\BackupsTool(
            \constant('BACKUP_DIRECTORY'),
            $company.'.*\.winstrom-backup',
        ),
    ));

    $oPage->addItem(new ui\PageBottom());

    $oPage->draw();
} else {
    if ($loader->deleteFromAbraFlexi()) {
        $loader->addStatusMessage(_('company removed before restore'), 'warning');
    } else {
        $loader->addStatusMessage(_('company cleanup failed'), 'warning');
    }

    if ($loader->restoreBackupFrom(\constant('BACKUP_DIRECTORY').$company.'.winstrom-backup')) {
        $loader->addStatusMessage(_('backup restored'), 'success');
    } else {
        $loader->addStatusMessage(sprintf(
            _('company %s was not restored'),
            $company,
        ), 'warning');
    }

    $oPage->redirect('company.php?company='.$company);
}
