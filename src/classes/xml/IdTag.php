<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\xml;

/**
 * Description of CustomButton
 *
 * @author vitex
 */
class IdTag extends \Ease\Html\PairTag
{
    public function __construct($id)
    {
        parent::__construct('id', null, $id);
    }
}
