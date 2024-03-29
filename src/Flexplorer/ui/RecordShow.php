<?php

/**
 * Flexplorer - přehled dat záznamu.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class RecordShow extends \Ease\TWB5\Panel
{
    /**
     * Zobrazí přehled záznamu.
     *
     * @param \Flexplorer\Flexplorer $recordObject
     * @param mixed $bootom Obsah spodní části panelu
     */
    public function __construct($recordObject, $bottom = null)
    {
        $evidence = $recordObject->getEvidence();
        parent::__construct(
            new \Ease\Html\H3Tag(
                new \Ease\Html\ATag(
                    'evidence.php?evidence=' . $evidence,
                    $evidence . ' ' . $recordObject
                )
            ),
            'warning',
            null,
            $bottom
        );

        $this->addItem('ExtID:' . $recordObject->getExternalID());

        $row = new \Ease\TWB5\Row();

        if (method_exists($recordObject, 'htmlizeRow')) {
            $recordObject->setData($recordObject->htmlizeRow($recordObject->getData()));
        }

        foreach ($recordObject->evidenceStructure as $keyword => $kinfo) {
            if ($keyword == $recordObject->nameColumn) {
                continue;
            }
            if (isset($kinfo['title'])) {
                $def = new \Ease\Html\DlTag();
                $def->addDef(
                    $kinfo['title'],
                    $recordObject->getDataValue($keyword)
                );
                $row->addItem(new \Ease\TWB5\Col(4, $def));
            }
        }

        $this->addItem($row);
    }
}
