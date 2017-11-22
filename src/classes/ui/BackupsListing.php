<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of BackupsListing
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class BackupsListing extends \Ease\Html\DivTag
{

    /**
     *
     * @var \Ease\Html\TableTag
     */
    public $contents = null;

    public function __construct($backupDir, $regex, $properties = [])
    {
        parent::__construct(new \Ease\Html\H1Tag($backupDir), $properties);
        $this->contents = new \Ease\Html\TableTag('', ['class' => 'table']);
        foreach ($this->getListing($backupDir, $regex) as $fileInfo) {
            $this->addFileToListing($fileInfo);
        }
        $this->contents->addRowHeaderColumns([_('File'), _('Size'), _('Age')]);
        $this->addItem($this->contents);
    }

    public function getListing($backupDir, $regex)
    {
        $files = [];
        $d     = dir($backupDir);
        while (false !== ($entry = $d->read())) {
            if (preg_match("/$regex/", $entry)) {
                $files[$entry] = [
                    'filename' => $entry,
                    'path' => $backupDir.'/'.$entry,
                    'size' => filesize($backupDir.'/'.$entry),
                    'age' => filemtime($backupDir.'/'.$entry)
                ];
            }
        }
        $d->close();
        return $files;
    }

    public function addFileToListing($fileInfo)
    {
        $this->contents->addRowColumns([
            $fileInfo['filename'],
            \Ease\Page::humanFilesize($fileInfo['size']),
            new ShowLiveAge($fileInfo['age'])
        ]);
    }
}
