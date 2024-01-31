<?php

/**
 * Flexplorer - textarea pro vložení jsonu
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of JsonForm
 *
 * @author vitex
 */
class JsonTextarea extends \Ease\TWB5\Textarea
{
    /**
     * Add scripts
     */
    public function finalize()
    {
        WebPage::singleton()->includeJavaScript('js/jquery.autosize.min.js');
        WebPage::singleton()->addJavaScript('
         var textarea = $("textarea[name=\'' . $this->getTagName() . '\']");
         textarea.autosize();
         var unformated = textarea.val();
         if( unformated != "" ) {
            var formated = JSON.stringify($.parseJSON( unformated ),undefined, 4);
            textarea.val(formated)
         }
        ');
    }
}
