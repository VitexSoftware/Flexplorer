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

namespace Flexplorer\ui;

/**
 * Description of BackupsTool.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class BackupsTool extends BackupsListing
{
    public function __construct($backupDir, $regex, $properties = [])
    {
        parent::__construct($backupDir, $regex, $properties);
        $this->header->addItem(new \Ease\Html\ThTag(_('Restore')));
        $this->header->addItem(new \Ease\Html\ThTag(_('Delete')));
    }

    public function addFileToListing($fileInfo): void
    {
        $infoRow = parent::addFileToListing($fileInfo);
        $infoRow->addItem(new \Ease\Html\TdTag(new \Ease\TWB5\LinkButton(
            'restorecompany.php?backup='.$fileInfo['filename'],
            _('Restore'),
            'success',
        )));
        $infoRow->addItem(new \Ease\Html\TdTag(new \Ease\TWB5\LinkButton(
            '?delete='.$fileInfo['filename'],
            _('Delete'),
            'danger',
        )));
    }
}
