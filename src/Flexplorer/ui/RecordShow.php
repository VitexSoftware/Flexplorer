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

class RecordShow extends \Ease\TWB5\Panel
{
    /**
     * Zobrazí přehled záznamu.
     *
     * @param \Flexplorer\Flexplorer $recordObject
     * @param null|mixed             $bottom
     */
    public function __construct($recordObject, $bottom = null)
    {
        $evidence = $recordObject->getEvidence();
        parent::__construct(
            new \Ease\Html\H3Tag(
                new \Ease\Html\ATag(
                    'evidence.php?evidence='.$evidence,
                    $evidence.' '.$recordObject,
                ),
            ),
            'warning',
            null,
            $bottom,
        );

        $this->addItem('ExtID:'.$recordObject->getExternalID());

        $row = new \Ease\TWB5\Row();

        if (method_exists($recordObject, 'htmlizeRow')) {
            $recordObject->setData($recordObject->htmlizeRow($recordObject->getData()));
        }

        foreach ($recordObject->evidenceStructure as $keyword => $kinfo) {
            if ($keyword === $recordObject->nameColumn) {
                continue;
            }

            if (isset($kinfo['title'])) {
                $def = new \Ease\Html\DlTag();
                $def->addDef(
                    $kinfo['title'],
                    $recordObject->getDataValue($keyword),
                );
                $row->addItem(new \Ease\TWB5\Col(4, $def));
            }
        }

        $this->addItem($row);
    }
}
