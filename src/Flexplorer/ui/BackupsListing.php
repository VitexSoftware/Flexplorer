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
 * Description of BackupsListing.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class BackupsListing extends \Ease\Html\DivTag
{
    public \Ease\Html\ThTag $header = null;
    public \Ease\Html\TableTag $contents = null;

    /**
     * Show basic directory listing.
     *
     * @param string $backupDir
     * @param string $regex
     * @param array  $properties
     */
    public function __construct($backupDir, $regex, $properties = [])
    {
        parent::__construct(new \Ease\Html\H1Tag($backupDir), $properties);
        $this->contents = new \Ease\Html\TableTag('', ['class' => 'table']);

        foreach ($this->getListing($backupDir, $regex) as $fileInfo) {
            $this->addFileToListing($fileInfo);
        }

        $this->header = $this->contents->addRowHeaderColumns([_('File'), _('Size'),
            _('Age')]);
        $this->addItem($this->contents);
    }

    public function getListing($backupDir, $regex)
    {
        $files = [];
        $d = dir($backupDir);

        while (false !== ($entry = $d->read())) {
            if (preg_match("/{$regex}/", $entry)) {
                $files[$entry] = [
                    'filename' => $entry,
                    'path' => $backupDir.'/'.$entry,
                    'size' => filesize($backupDir.'/'.$entry),
                    'age' => filemtime($backupDir.'/'.$entry),
                ];
            }
        }

        $d->close();

        return $files;
    }

    public function addFileToListing($fileInfo)
    {
        return $this->contents->addRowColumns([
            $fileInfo['filename'],
            \Ease\Page::humanFilesize($fileInfo['size']),
            new \Ease\ui\LiveAg((new \DateTime())->setTimestamp((int) $fileInfo['age'])),
        ]);
    }
}
