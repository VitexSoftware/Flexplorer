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
 * Description of UI\FXPreloader.
 *
 * @author vitex
 */
class FXPreloader extends \Ease\Html\DivTag
{
    public function __construct($id = null)
    {
        parent::__construct(
            null,
            ['class' => 'loader', 'data-initialize' => 'loader', 'id' => $id],
        );
    }

    public function finalize(): void
    {
        WebPage::singleton()->includeCss(
            'https://cdnjs.cloudflare.com/ajax/libs/fuelux/3.16.7/css/fuelux.css',
            true,
        );
        WebPage::singleton()->includeJavascript('https://cdnjs.cloudflare.com/ajax/libs/fuelux/3.16.7/js/fuelux.js');
        WebPage::singleton()->addJavascript("$('#".$this->getTagID()."').loader();");
        WebPage::singleton()->addCSS(<<<'EOD'

#
EOD.$this->getTagID().<<<'EOD'
{
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: -50px;
    margin-left: -50px;
    width: 100px;
    height: 100px;
    visibility: hidden;S
}

EOD);
    }
}
