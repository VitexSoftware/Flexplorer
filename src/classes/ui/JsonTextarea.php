<?php
/**
 * Flexplorer - textarea pro vložení jsonu
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of JsonForm
 *
 * @author vitex
 */
class JsonTextarea extends \Ease\TWB\Textarea
{

    /**
     * TODO:  https://ace.c9.io/
     */
    function finalize()
    {
        \Ease\Shared::webPage()->includeJavaScript('js/jquery.autosize.min.js');
        \Ease\Shared::webPage()->addJavaScript('
         var textarea = $("textarea[name=\''.$this->getTagName().'\']");
         textarea.autosize();
         var unformated = textarea.val();
         if( unformated != "" ) {
            var formated = JSON.stringify($.parseJSON( unformated ),undefined, 4);
            textarea.val(formated)
         }
        ');
    }
}