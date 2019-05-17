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
class DateTimePicker extends \Ease\Html\DivTag
{
    public $format = null;

    public function __construct($name, $value = null, $properties = null,
                                $addScript = null)
    {
        $properties['class'] = 'form-control';
        if (isset($properties['data-format'])) {
            $this->format = $properties['data-format'];
        }
        parent::__construct(new \Ease\Html\InputTextTag($name, $value,
                $properties));
        $this->setTagId($name);
        $this->addItem(new \Ease\Html\Span(new \Ease\TWB\GlyphIcon('calendar'),
                ['class' => 'input-group-addon']));
        \Ease\Shared::webPage()->addJavascript("$('#".$this->getTagID()."').datetimepicker({
            locale: \"cs\",
            format: \"".$this->format."\",
            showTodayButton: true,
            showClear: true,
            showClose: true,
            });");

        if ($addScript) {

            \Ease\Shared::webPage()->addJavascript("$('#".$this->getTagID()."').datetimepicker().on('dp.change',function(e){
    ".$addScript.";
}) ;");
        }
    }

    public function finalize()
    {
        \Ease\Shared::webPage()->includeCss('css/bootstrap-datetimepicker.min.css');
        \Ease\Shared::webPage()->includeJavaScript('js/moment-with-locales.js');
        \Ease\Shared::webPage()->includeJavaScript('js/bootstrap-datetimepicker.js');
        $this->setTagClass('input-group datetime');
        parent::finalize();
    }
}
