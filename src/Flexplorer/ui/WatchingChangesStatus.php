<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of WatchingChangesStatus
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class WatchingChangesStatus extends \Ease\Html\SpanTag
{
    /**
     *
     * @param boolean $status
     * @param options $properties
     */
    public function __construct($status, $properties = [])
    {
        parent::__construct(new BooleanLabel($status), $properties);
        $this->addItem(new \Ease\TWB5\LinkButton(
            'changesapi.php',
            '🔧',
            'default btn-sm',
            ['title' => _('Chanes API Settings')]
        ));
    }
}
