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
class UrlTag extends \Ease\Html\PairTag
{
    public function __construct($url)
    {
        parent::__construct('url', null, '<![CDATA[' . $url . ']]>');
    }
}
