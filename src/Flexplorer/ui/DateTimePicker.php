<?php

/**
 * Flexplorer - volba data a času.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of Datepicker
 *
 * @author vitex
 */
class DateTimePicker extends \Ease\Html\DivTag {

    public $format = null;

    public function __construct($name, $value = null, $properties = null,
            $addScript = null) {
        $properties['class'] = 'form-control';
        if (isset($properties['data-format'])) {
            $this->format = $properties['data-format'];
        }
        parent::__construct(new \Ease\Html\InputTextTag($name, $value,
                        $properties));
        $this->setTagId($name);
        $this->addItem(new \Ease\Html\Span(new \Ease\TWB5\GlyphIcon('calendar'),
                        ['class' => 'input-group-addon']));
        WebPage::singleton()->addJavascript("$('#" . $this->getTagID() . "').datetimepicker({
            locale: \"cs\",
            format: \"" . $this->format . "\",
            showTodayButton: true,
            showClear: true,
            showClose: true,
            });");

        if ($addScript) {

            WebPage::singleton()->addJavascript("$('#" . $this->getTagID() . "').datetimepicker().on('dp.change',function(e){
    " . $addScript . ";
}) ;");
        }
    }

    public function finalize() {
        WebPage::singleton()->includeCss('css/bootstrap-datetimepicker.min.css');
        WebPage::singleton()->includeJavaScript('js/moment-with-locales.js');
        WebPage::singleton()->includeJavaScript('js/bootstrap-datetimepicker.js');
        $this->setTagClass('input-group datetime');
        parent::finalize();
    }

}
