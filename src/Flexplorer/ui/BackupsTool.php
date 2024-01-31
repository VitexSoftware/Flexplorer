<?php

namespace Flexplorer\ui;

/**
 * Description of BackupsTool
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class BackupsTool extends BackupsListing {

    function __construct($backupDir, $regex, $properties = array()) {
        parent::__construct($backupDir, $regex, $properties);
        $this->header->addItem(new \Ease\Html\ThTag(_('Restore')));
        $this->header->addItem(new \Ease\Html\ThTag(_('Delete')));
    }

    public function addFileToListing($fileInfo) {
        $infoRow = parent::addFileToListing($fileInfo);
        $infoRow->addItem(new \Ease\Html\TdTag(new \Ease\TWB5\LinkButton('restorecompany.php?backup=' . $fileInfo['filename'],
                                _('Restore'), 'success')));
        $infoRow->addItem(new \Ease\Html\TdTag(new \Ease\TWB5\LinkButton('?delete=' . $fileInfo['filename'],
                                _('Delete'), 'danger')));
    }

}
