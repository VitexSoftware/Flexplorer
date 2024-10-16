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
 * Description of BsGrid.
 *
 * @author vitex
 */
class BsGrid extends \Ease\Html\Div
{
    /**
     * Pole vlastností gridu.
     */
    public array $options = [];

    public function __construct($id, $properties = null)
    {
        $properties['id'] = $id;
        parent::__construct(null, $properties);
    }

    public function finalize(): void
    {
        WebPage::singleton()->includeCss('css/jquery-ui-timepicker-addon.min.css');
        WebPage::singleton()->includeJavaScript('js/jquery-ui-timepicker-addon.min.js');
        WebPage::singleton()->includeJavaScript('js/jquery.ui.touch-punch.min.js');

        WebPage::singleton()->includeCss('css/jquery.bs_pagination.min.css');
        WebPage::singleton()->includeJavaScript('js/jquery.bs_pagination.min.js');
        WebPage::singleton()->includeJavaScript('js/bs_pagination/localization/en.min.js');

        WebPage::singleton()->includeCss('css/jquery.jui_filter_rules.bs.min.css');
        WebPage::singleton()->includeJavaScript('js/jquery.jui_filter_rules.min.js');
        WebPage::singleton()->includeJavaScript('js/jui_filter_rules/localization/en.min.js');
        WebPage::singleton()->includeJavaScript('js/moment.min.js');

        WebPage::singleton()->includeCss('css/jquery.bs_grid.min.css');
        WebPage::singleton()->includeJavaScript('js/jquery.bs_grid.min.js');
        WebPage::singleton()->includeJavaScript('js/bs_grid/localization/en.min.js');

        WebPage::singleton()->addJavaScript(<<<'EOD'

$("#
EOD.$this->getTagID().'").bs_grid('.json_encode($this->options).<<<'EOD'
);

EOD);
        \Ease\JQuery\UIPart::jQueryze();
    }
}
