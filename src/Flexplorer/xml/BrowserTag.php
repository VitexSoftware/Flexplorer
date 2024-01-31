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
class BrowserTag extends \Ease\Html\PairTag
{
    /**
     *
     * @param type $content
     */
    public function __construct($content = null)
    {
        parent::__construct('browser', null, $content);
    }
}
