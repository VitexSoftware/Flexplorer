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

        $buttonTabs = new \Ease\TWB5\Tabs('butonTabs');

        if (!empty($buttons)) {
            $buttonsTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
            $buttonsTable->addRowHeaderColumns([_('Code'), _('Url'), _('Button'),
                _('Edit'), _('Delete')]);
            foreach ($buttons as $button) {
                $button['title'] = new \Ease\TWB5\LinkButton(
                    $button['url'],
                    $button['title'],
                    ($button['location'] == 'list') ? 'info' : 'default',
                    ['title' => $button['description']]
                );
                unset($button['description']);
                unset($button['location']);

                $button['edit'] = new \Ease\TWB5\LinkButton(
                    'editor.php?evidence=custom-button&id=' . $button['id'],
                    new \Ease\TWB5\GlyphIcon('wrench'),
                    'warning'
                );
                $button['delete'] = new \Ease\TWB5\LinkButton(
                    'delete.php?evidence=custom-button&action=delete&id=' . $button['id'],
                    new \Ease\TWB5\GlyphIcon('remove'),
                    'danger'
                );

                unset($button['id']);
                $buttonsTable->addRowColumns($button);
            }
            $buttonTabs->addTab(_('Current Buttons'), $buttonsTable);
        }

        $buttonTabs->addTab(
            _('New Button'),
            new \Ease\TWB5\Container(new ButtonForm($source->getEvidence()))
        );

        $this->addItem(new \Ease\TWB5\Container($buttonTabs));
    }

    /**
     * List of buttons for Current Evidence
     *
     * @param \AbraFlexi\RO $source
     *
     * @return array
     */
    public function getButtonsForEvidence($source)
    {
        $buttoner = new \AbraFlexi\RO(
            null,
            array_merge(
                $source->getConnectionOptions(),
                ['evidence' => 'custom-button']
            )
        );
        return $buttoner->getColumnsFromAbraFlexi(['kod', 'url', 'title', 'description',
                    'location'], ['evidence' => $source->getEvidence()]);
    }
}
