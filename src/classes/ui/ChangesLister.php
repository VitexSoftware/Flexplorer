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
        $pageItem = new \Ease\Html\ATag('change.php?file='.$pageItem,
            str_replace(['flexplorer-changes-', '_'], ['', '&nbsp;<strong>'],
                $pageItem).'</strong>');
        $item     = parent::addItemSmart($pageItem, $properties);
        return $item;
    }
}
