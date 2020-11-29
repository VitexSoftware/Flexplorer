<?php

/**
 * Flexplorer - RecordEditor
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2018 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of RecordEditor
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class RecordEditor extends \Ease\TWB\Panel {

    /**
     *
     * @var ColumnsForm
     */
    public $form = null;

    /**
     *
     * @var \AbraFlexi\RW
     */
    public $engine = null;

    /**
     * Ajax Record Editor
     * @param \AbraFlexi\RW $engine
     */
    public function __construct($engine) {
        parent::__construct(new \Ease\Html\H3Tag(new \Ease\Html\ATag('evidence.php?evidence=' . $engine->getEvidence(),
                                \AbraFlexi\EvidenceList::$evidences[$engine->getEvidence()]['evidenceName']) . ' #' . $engine->getMyKey()),
                'info');
        $columns = $engine->getColumnsInfo();
        WebPage::singleton()->includeJavaScript('js/datasaver.js');
        $this->form = new ColumnsForm($engine);


        $this->form->addItem(new \Ease\Html\InputHiddenTag($engine->getKeyColumn(),
                        $engine->getMyKey(), ['class' => 'keyId']));

        $this->form->addItem(new \Ease\Html\InputHiddenTag('evidence',
                        $engine->getEvidence(), ['class' => 'evidence']));

        $this->engine = $engine;
        foreach ($engine->evidenceStructure as $columnName => $column) {
            if (($columnName != 'id') && ( $columnName != 'external-ids' )) {
                $this->addFlexiInput($column);
            }
        }

        if (empty($engine->getMyKey())) {
            $this->form->addInput(new \Ease\TWB\SubmitButton(_('Save new record'),
                            'success'));
        }

        $this->addItem($this->form);
    }

    /**
     * Add an AbraFlexi column type input field
     *
     * @param array $colProperties
     */
    public function addFlexiInput($colProperties) {
        $type = $colProperties['type'];
        $name = $colProperties['name'];
        $propertyName = isset($colProperties['propertyName']) ? $colProperties['propertyName'] : $colProperties['name'];
        $value = $this->engine->getDataValue($propertyName);
        $note = '';

        $inputProperties = [];

        if (($type != 'logic') && ($type != 'date')) {
            $inputProperties['OnChange'] = $this->onChangeCode($propertyName);
        }

        if (isset($colProperties['mandatory']) && ($colProperties['mandatory'] === 'true')) {
            $inputProperties[] = 'required';
            $note .= '<span class="error">' . _('Required') . '</span> ';
        }
        if (isset($colProperties['isWritable']) && ($colProperties['isWritable'] === 'false')) {
            $inputProperties[] = 'disabled';
            return;
        }

        if (is_null($value)) {
            $placeholder = 'null';
        } else {
            $placeholder = $value;
        }

        switch ($type) {
            case 'numeric':
                $inputProperties['pattern'] = '^\d+(\.|\,)\d{2}$';
                $widget = new \Ease\Html\InputNumberTag($propertyName,
                        $value, $inputProperties);
                break;
            case 'integer':
                $widget = new \Ease\Html\InputNumberTag($propertyName,
                        $value, $inputProperties);
                break;
            case 'logic':

                $widget = new YesNoSwitch($propertyName, $value, true,
                        $inputProperties);
                break;
            case 'relation':
                $evidence = '';
                if (isset($colProperties['url'])) {
                    $tmp = explode('/', $colProperties['url']);
                    $evidence = end($tmp);
                }
                $colProperties['data-evidence'] = $evidence;
                $widget = new RelationSelect($propertyName,
                        $value, $inputProperties);

                $note = [new \Ease\Html\ATag('evidence.php?evidence=' . $evidence,
                            new \Ease\TWB\GlyphIcon('list') . ' ' . $evidence)];

                if (strlen($value)) {
                    $note[] = new \Ease\Html\ATag('editor.php?evidence=' . $evidence . '&id=' . urlencode($value),
                            new \Ease\TWB\GlyphIcon('edit') . ' ' . _('Edit targeted record'));
                }
                break;

            case 'select':
                $widget = new \Ease\Html\Select($propertyName,
                        $this->colValues($colProperties), $value, null,
                        $inputProperties);
                break;
            case 'date':
                $inputProperties['data-format'] = 'YYYY-MM-DD';
                $widget = new DateTimePicker($propertyName,
                        $value, $inputProperties, $this->onChangeCode($propertyName));
                break;
            case 'datetime':
                $inputProperties['data-format'] = 'YYYY-MM-DDTHH:mm:ss';
                $widget = new DateTimePicker($propertyName,
                        $value, $inputProperties, $this->onChangeCode($propertyName));
                break;
            case 'string':
                $widget = new \Ease\Html\InputTextTag($propertyName,
                        $value, $inputProperties);
                break;
            default:
                $this->addStatusMessage(sprintf(_('Unknown type of data %s'),
                                $type), 'warning');
                $widget = new \Ease\Html\InputTag($propertyName,
                        $value, $inputProperties);
                $note = '?: ' . $type;
                break;
        }


        if (is_array($note)) {
            $note[] = ' ' . _('Type') . ': ' . $type;
        } else {
            $note .= ' ' . _('Type') . ': ' . $type;
        }

        $this->form->addInput($widget, $propertyName . ': ' . $name, $placeholder,
                $note);
    }

    /**
     * Vrací pole možností pro select
     *
     * @param array $colProperties
     * @return array
     */
    private function colValues($colProperties) {
        $options = [];
        if (isset($colProperties['values'])) {
            foreach ($colProperties['values']['value'] as $colValue) {
                $options[$colValue['@key']] = $colValue['$'];
            }
        }
        return $options;
    }

    /**
     * Vraci kod pro ukladani policka formulare po editaci
     *
     * @param string $fieldName
     * 
     * @return string javascript
     */
    public function onChangeCode($fieldName) {
        $chCode = '';
        $id = $this->engine->getMyKey();
        if (!is_null($id)) {
            $chCode = 'saveColumnData(\'' . str_replace('\\', '-',
                            get_class($this->engine)) . '\', \'' . $id . '\', \'' . $fieldName . '\', \'' . $this->engine->getEvidence() . '\')';
        }
        return $chCode;
    }

}
