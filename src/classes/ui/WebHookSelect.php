<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of WebHookSelect
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class WebHookSelect extends \Ease\Html\Select
{
    public function __construct($name, $items = null, $defaultValue = null,
                                $properties = array())
    {
        $items  = [];
        $hooker = new \FlexiPeeHP\Hooks();
        $hooks  = $hooker->getFlexiData();
        if (!isset($hooks['message']) && count($hooks) && count(current($hooks))) {
            foreach ($hooks as $hook) {
                    $items = [$hook['url'] => $hook['dataFormat'].' '.$hook['url']];
            }
        }
        parent::__construct($name, $items, $defaultValue, null, $properties);
    }
}