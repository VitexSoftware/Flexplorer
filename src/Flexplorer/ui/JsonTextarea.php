<?php

declare(strict_types=1);

/**
 * This file is part of the Flexplorer package
 *
 * github.com/VitexSoftware/Flexplorer
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flexplorer\ui;

/**
 * Description of JsonForm.
 *
 * @author vitex
 */
class JsonTextarea extends \Ease\Html\TextareaTag
{
    /**
     * Add scripts.
     */
    public function finalize(): void
    {
        WebPage::singleton()->includeJavaScript('js/jquery.autosize.min.js');
        WebPage::singleton()->addJavaScript(<<<'EOD'

         var textarea = $("textarea[name='
EOD.$this->getTagName().<<<'EOD'
']");
         textarea.autosize();
         var unformated = textarea.val();
         if( unformated != "" ) {
            var formated = JSON.stringify($.parseJSON( unformated ),undefined, 4);
            textarea.val(formated)
         }

EOD);
    }
}
