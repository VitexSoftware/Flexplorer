<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of CopyToClipBoard
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class CopyToClipBoard extends \Ease\Container {

    public function __construct($initialContent = null) {
        parent::__construct($initialContent);
        $this->addItem(new \Ease\Html\ButtonTag(new \Ease\TWB5\GlyphIcon('copy'),
                        ['class' => 'btn copy', 'data-clipboard-target' => '#' . $initialContent->getTagID()]));
        WebPage::singleton()->includeJavaScript('js/clipboard.js');
        WebPage::singleton()->addJavaScript('var clipboard = new Clipboard(\'.copy\');');
    }

}
