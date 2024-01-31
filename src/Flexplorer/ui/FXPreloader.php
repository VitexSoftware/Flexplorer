<?php

namespace Flexplorer\ui;

/**
 * Description of UI\FXPreloader
 *
 * @author vitex
 */
class FXPreloader extends \Ease\Html\DivTag
{
    public function __construct($id = null)
    {
        parent::__construct(
            null,
            ['class' => 'loader', 'data-initialize' => 'loader', 'id' => $id]
        );
    }

    public function finalize()
    {
        WebPage::singleton()->includeCss(
            'https://cdnjs.cloudflare.com/ajax/libs/fuelux/3.16.7/css/fuelux.css',
            true
        );
        WebPage::singleton()->includeJavascript('https://cdnjs.cloudflare.com/ajax/libs/fuelux/3.16.7/js/fuelux.js');
        WebPage::singleton()->addJavascript("$('#" . $this->getTagID() . "').loader();");
        WebPage::singleton()->addCSS('
#' . $this->getTagID() . '{
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: -50px;
    margin-left: -50px;
    width: 100px;
    height: 100px;
    visibility: hidden;S
}​
            ');
    }
}
