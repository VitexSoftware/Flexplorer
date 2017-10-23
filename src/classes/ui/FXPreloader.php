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
        parent::__construct(null,
            ['class' => 'loader', 'data-initialize' => 'loader', 'id' => $id]);
    }

    public function finalize()
    {
        \Ease\Shared::webPage()->includeCss('twitter-bootstrap/css/fuelux.css',
            true);
        \Ease\Shared::webPage()->includeJavascript("/javascript/twitter-bootstrap/fuelux.js");
        \Ease\Shared::webPage()->addJavascript("$('#".$this->getTagID()."').loader();");
        \Ease\Shared::webPage()->addCSS('
#'.$this->getTagID().'{
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: -50px;
    margin-left: -50px;
    width: 100px;
    height: 100px;
    visibility: hidden;
}​
            ');
    }

}
