<?php
/**
 * Flexplorer - bs_grid.
 *
 * @link http://www.pontikis.net/labs/bs_grid/ Bootstrap Datagrid
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of BsGrid
 *
 * @author vitex
 */
class BsGrid extends \Ease\Html\Div
{
    /**
     * Pole vlastností gridu
     * @var array
     */
    public $options = [];

    public function __construct($id, $properties = null)
    {
        $properties['id'] = $id;
        parent::__construct(null, $properties);
    }

    public function finalize()
    {

        \Ease\Shared::webPage()->includeCss('css/jquery-ui-timepicker-addon.min.css');
        \Ease\Shared::webPage()->includeJavaScript('js/jquery-ui-timepicker-addon.min.js');
        \Ease\Shared::webPage()->includeJavaScript('js/jquery.ui.touch-punch.min.js');

        \Ease\Shared::webPage()->includeCss('css/jquery.bs_pagination.min.css');
        \Ease\Shared::webPage()->includeJavaScript('js/jquery.bs_pagination.min.js');
        \Ease\Shared::webPage()->includeJavaScript('js/bs_pagination/localization/en.min.js');

        \Ease\Shared::webPage()->includeCss('css/jquery.jui_filter_rules.bs.min.css');
        \Ease\Shared::webPage()->includeJavaScript('js/jquery.jui_filter_rules.min.js');
        \Ease\Shared::webPage()->includeJavaScript('js/jui_filter_rules/localization/en.min.js');
        \Ease\Shared::webPage()->includeJavaScript('js/moment.min.js');

        \Ease\Shared::webPage()->includeCss('css/jquery.bs_grid.min.css');
        \Ease\Shared::webPage()->includeJavaScript('js/jquery.bs_grid.min.js');
        \Ease\Shared::webPage()->includeJavaScript('js/bs_grid/localization/en.min.js');


        \Ease\Shared::webPage()->addJavaScript('
$("#'.$this->getTagID().'").bs_grid('.json_encode($this->options).');
            ');
        \Ease\JQuery\UIPart::jQueryze();
    }
}