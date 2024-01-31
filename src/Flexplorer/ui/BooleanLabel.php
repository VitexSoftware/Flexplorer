<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of BooleanLabel
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class BooleanLabel extends \Ease\TWB5\Label {

    /**
     * Show boolean Label
     *
     * @param boolean $bool       state
     * @param array   $properties options
     */
    public function __construct($bool, $properties = []) {
        parent::__construct($bool ? 'success' : 'default',
                $bool ? _('Yes') : _('No'), $properties);
    }

}
