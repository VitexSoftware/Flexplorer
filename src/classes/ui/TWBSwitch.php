<?php
/**
 * Flexplorer - přepínač.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class TWBSwitch extends \Ease\Html\CheckboxTag
{
    public $properties = [];

    /**
     * Zobrazuje HTML Checkbox
     *
     * @param string $name       jméno tagu
     * @param bool   $checked    stav checkboxu
     * @param string $value      vracená hodnota checkboxu
     * @param array  $properties parametry tagu
     */
    public function __construct($name, $checked = false, $value = null,
                                $properties = null)
    {
        parent::__construct($name, $checked, $value, $properties);
        if (!isset($properties['onText'])) {
            $properties['onText'] = _('Ano');
        }
        if (!isset($properties['offText'])) {
            $properties['offText'] = _('Ne');
        }

        $this->setProperties($properties);
    }

    function setProperties($properties)
    {
        $this->properties = array_merge($this->properties, $properties);
    }

    function finalize()
    {
        \Ease\TWB\Part::twBootstrapize();
        $this->includeCss('/javascript/twitter-bootstrap/css/bootstrap-switch.css');
        $this->includeJavascript('/javascript/twitter-bootstrap/js/bootstrap-switch.js');
        $this->addJavascript('$("[name=\''.$this->getTagName().'\']").bootstrapSwitch({'.\Ease\TWB\Part::partPropertiesToString($this->properties).'})',
            null, true);
    }
}