<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of ChangesLister
 *
 * @author vitex
 */
class ChangesLister extends \Ease\Html\UlTag
{
    public function &addItemSmart($pageItem, $properties = [])
    {
        list($tmp, $stamp) = explode('_', $pageItem);
        $age = new \Ease\ui\LiveAge(str_replace('.json', '', $stamp));
        $pageItem = new \Ease\Html\ATag(
            'change.php?file=' . $pageItem,
            str_replace(
                ['flexplorer-changes-', '_'],
                ['', '&nbsp;<strong>'],
                $pageItem
            ) . '</strong> ' . $age
        );
        $item = parent::addItemSmart($pageItem, $properties);
        return $item;
    }
}
