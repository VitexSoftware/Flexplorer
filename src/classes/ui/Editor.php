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
class Editor extends ColumnsForm
{

    /**
     *
     * @param \Flexplorer\Flexplorer $engine
     */
    public function __construct($engine)
    {
        parent::__construct($engine);
        foreach ($engine->evidenceStructure as $column) {
            $this->addFlexiInput($column);
        }
        $this->addItem(new TWBSwitch('toFlexiBee', false, 'on',
            ['onText' => _('Uložit do FlexiBee'), 'offText' => _('Zobrazit v editoru')]));
        $this->addItem(new \Ease\TWB\SubmitButton(_('OK').' '.new \Ease\TWB\GlyphIcon('save')));
    }

    public function addFlexiInput($colProperties)
    {
        $type         = $colProperties['type'];
        $name         = $colProperties['name'];
        $propertyName = $colProperties['propertyName'];
        $value        = $this->engine->getDataValue($propertyName);
        $note         = '';

        $inputProperties = [];
        if ($colProperties['mandatory'] === 'true') {
            $inputProperties[] = 'required';
            $note .= '<span class="error">'._('Povinný').'</span> ';
        }
        if (isset($colProperties['isWritable']) && ($colProperties['isWritable']
            === 'false')) {
            $inputProperties[] = 'disabled';
        }

        if (is_null($value)) {
            $placeholder = 'null';
        } else {
            $placeholder = $value;
        }

        switch ($type) {
            case 'numeric':
                $inputProperties['pattern'] = '^\d+(\.|\,)\d{2}$';
                $widget                     = new \Ease\Html\InputNumberTag($propertyName,
                    $value, $inputProperties);
                break;
            case 'integer':
                $widget                     = new \Ease\Html\InputNumberTag($propertyName,
                    $value, $inputProperties);
                break;
            case 'logic':
                $name                       = $name;
                $widget                     = new TWBSwitch($propertyName,
                    $value, $inputProperties);
                break;
            case 'relation':
                $evidence                   = '';
                if (isset($colProperties['url'])) {
                    $tmp      = explode('/', $colProperties['url']);
                    $evidence = end($tmp);
                }
                $colProperties['data-evidence'] = $evidence;
                $widget                         = new RelationSelect($propertyName,
                    $value, $inputProperties);

                $note = [new \Ease\Html\ATag('evidence.php?evidence='.$evidence,
                        new \Ease\TWB\GlyphIcon('list').' '.$evidence)];

                if (strlen($value)) {
                    $note[] = new \Ease\Html\ATag('editor.php?evidence='.$evidence.'&id='.urlencode($value),
                        new \Ease\TWB\GlyphIcon('edit').' '._('Uprav odkazovaný záznam'));
                }
                break;

            case 'select':
                $widget                         = new \Ease\Html\Select($propertyName,
                    $this->colValues($colProperties), $value, null,
                    $inputProperties);
                break;
            case 'date':
                $inputProperties['data-format'] = 'YYYY-MM-DD+01:00';
                $widget                         = new DateTimePicker($propertyName,
                    $value, $inputProperties);
                break;
            case 'datetime':
                $inputProperties['data-format'] = 'YYYY-MM-DD\'T\'HH:mm:ss.SSS';
                $widget                         = new DateTimePicker($propertyName,
                    $value, $inputProperties);
                break;
            case 'string':
                $widget                         = new \Ease\Html\InputTextTag($propertyName,
                    $value, $inputProperties);
                break;
            default:
                $this->addStatusMessage(sprintf(_('Neznámý druh dat %s'), $type),
                    'warning');
                $widget                         = new \Ease\Html\InputTag($propertyName,
                    $value, $inputProperties);
                $note                           = '?: '.$type;
                break;
        }


        if (is_array($note)) {
            $note[] = ' '._('Typ').': '.$type;
        } else {
            $note .= ' '._('Typ').': '.$type;
        }

        $this->addInput($widget, $name, $placeholder, $note);
    }

    /**
     * Vrací pole možností pro select
     *
     * @param array $colProperties
     * @return array
     */
    private function colValues($colProperties)
    {
        $options = [];
        foreach ($colProperties['values']['value'] as $colValue) {
            $options[$colValue['@key']] = $colValue['$'];
        }
        return $options;
    }
}