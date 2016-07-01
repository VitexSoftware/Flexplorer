<?php
/**
 * Flexplorer - Editor záznamu evidence.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of Editor
 *
 * @author vitex
 */
class Editor extends \Ease\TWB\Form
{
    /**
     * Objekt pro zpracování dat
     * @var \Flexplorer\Flexplorer
     */
    public $engine = null;

    /**
     *
     * @param \Flexplorer\Flexplorer $engine
     */
    public function __construct($engine)
    {
        $this->engine &= $engine;
        parent::__construct('editor', 'editor.php', 'post');
        foreach ($engine->evidenceStructure as $column) {
            $this->addFlexiInput($column);
        }
        $this->addItem(new \Ease\TWB\SubmitButton(_('Uložit')));
    }

    public function addFlexiInput($colProperties)
    {
        $type  = $colProperties['type'];
        $name  = $colProperties['name'];
        $value = $this->getDataValue($colProperties['propertyName']);


        switch ($type) {
            case 'int':
                $widget = new \Ease\Html\InputNumberTag($name, $value);
                break;
            default:
                $widget = new \Ease\Html\InputTextTag($name, $value);
                break;
        }

        $this->addInput($widget, $name, $value, '');
    }
}