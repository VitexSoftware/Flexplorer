<?php
/**
 * Flexplorer - volba data a času.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of Datepicker
 *
 * @author vitex
 */
class DateTimePicker extends \Ease\Html\Div
{

    public function __construct($name, $value = null, $properties = null)
    {
        parent::__construct(new \Ease\Html\InputTextTag($name, $value,
            ['class' => 'form-control']), $properties);
        $this->addItem(new \Ease\Html\Span(new \Ease\TWB\GlyphIcon('calendar'),
            ['class' => 'input-group-addon']));
    }

    public function finalize()
    {
        \Ease\Shared::webPage()->includeCss('css/bootstrap-datetimepicker.min.css');
        \Ease\Shared::webPage()->addJavascript("$('#".$this->getTagID()."').datetimepicker({
            locale: \"cs\",
            format: \"".$this->getTagProperty('data-format')."\",
            showTodayButton: true,
            showClear: true,
            showClose: true,
            });");
        \Ease\Shared::webPage()->includeJavaScript('js/moment-with-locales.js');
        \Ease\Shared::webPage()->includeJavaScript('js/bootstrap-datetimepicker.js');
        $this->setTagClass('input-group datetime');
        parent::finalize();
    }
}