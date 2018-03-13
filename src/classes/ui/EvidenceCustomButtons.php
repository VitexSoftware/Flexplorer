<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of EvidenceCustomButtons
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class EvidenceCustomButtons extends \Ease\Html\DivTag
{

    /**
     * 
     * @param \Flexplorer\Flexplorer $source
     */
    public function __construct($source)
    {
        parent::__construct();
        $buttons = $this->getButtonsForEvidence($source);
        if (empty($buttons)) {
            $newButtonForm = new ButtonForm($source->getEvidence());
            $this->addItem(new \Ease\TWB\Container($newButtonForm));
        } else {
            $buttonsTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
            $buttonsTable->addRowHeaderColumns(_('Code'), _('Url'), _('Title'),
                _('Description'), _('Location'));
            foreach ($buttons as $button) {
                $buttonsTable->addRowColumns($button);
            }
            $this->addItem($buttonsTable);
        }
    }

    /**
     * List of buttons for Current Evidence
     * 
     * @param \FlexiPeeHP\FlexiBeeRO $source
     * 
     * @return array
     */
    public function getButtonsForEvidence($source)
    {
        $buttoner = new \FlexiPeeHP\FlexiBeeRO(null,
            array_merge($source->getConnectionOptions(),
                ['evidence' => 'custom-button']));
        return $buttoner->getColumnsFromFlexibee(['kod', 'url', 'title', 'description',
                'location'], ['evidence' => $source->getEvidence()]);
    }
}
