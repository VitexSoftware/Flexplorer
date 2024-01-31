<?php

/**
 * Flexplorer - Editor záznamu evidence.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
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
     * Data Source Object
     * @var \AbraFlexi
     */
    public $engine = null;

    /**
     *
     * @param \Flexplorer\Flexplorer $engine
     */
    public function __construct($engine)
    {
        parent::__construct($engine);
        $id = $this->engine->getDataValue('id');
        if (is_array($id)) {
            $externalIDs = [];
            $this->engine->setDataValue('id', current($id));
            array_shift($id);
            $this->engine->setDataValue('external-ids', $id);
        }


        foreach ($engine->evidenceStructure as $column) {
            $this->addFlexiInput($column);
        }

        WebPage::singleton()->includeJavaScript('js/datasaver.js');
        $this->addItem(new \Ease\TWB5\Widgets\TWBSwitch(
            'toAbraFlexi',
            false,
            'on',
            ['onText' => _('Save to AbraFlexi'), 'offText' => _('Show in editor')]
        ));
        $this->addItem(new \Ease\TWB5\SubmitButton(_('OK') . ' ' . new \Ease\TWB5\GlyphIcon('save')));
        $this->engine = $engine;
    }

    /**
     * Add an AbraFlexi column type input field
     *
     * @param array $colProperties
     */
    public function addFlexiInput($colProperties)
    {
        $type = $colProperties['type'];
        $name = $colProperties['name'];
        $propertyName = isset($colProperties['propertyName']) ? $colProperties['propertyName'] : $colProperties['name'];
        $value = $this->engine->getDataValue($propertyName);
        $note = '';

        $inputProperties = ['OnChange' => $this->onChangeCode($propertyName)];
        if (isset($colProperties['mandatory']) && ($colProperties['mandatory'] === 'true')) {
            $inputProperties[] = 'required';
            $note .= '<span class="error">' . _('Required') . '</span> ';
        }
        if (isset($colProperties['isWritable']) && ($colProperties['isWritable'] === 'false')) {
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
                $widget = new \Ease\Html\InputNumberTag(
                    $propertyName,
                    $value,
                    $inputProperties
                );
                break;
            case 'integer':
                $widget = new \Ease\Html\InputNumberTag(
                    $propertyName,
                    $value,
                    $inputProperties
                );
                break;
            case 'logic':
                $widget = new TWBSwitch(
                    $propertyName,
                    $value,
                    true,
                    $inputProperties
                );
                break;
            case 'relation':
                $evidence = '';
                if (isset($colProperties['url'])) {
                    $tmp = explode('/', $colProperties['url']);
                    $evidence = end($tmp);
                }
                $colProperties['data-evidence'] = $evidence;
                $widget = new RelationSelect(
                    $propertyName,
                    $value,
                    $inputProperties
                );

                $note = [new \Ease\Html\ATag(
                    'evidence.php?evidence=' . $evidence,
                    new \Ease\TWB5\GlyphIcon('list') . ' ' . $evidence
                )];

                if (strlen($value)) {
                    $note[] = new \Ease\Html\ATag(
                        'editor.php?evidence=' . $evidence . '&id=' . urlencode($value),
                        new \Ease\TWB5\GlyphIcon('edit') . ' ' . _('Edit targeted record')
                    );
                }
                break;

            case 'select':
                $widget = new \Ease\Html\SelectTag(
                    $propertyName,
                    $this->colValues($colProperties),
                    $value,
                    null,
                    $inputProperties
                );
                break;
            case 'date':
                $inputProperties['data-format'] = 'YYYY-MM-DD+01:00';
                $widget = new DateTimePicker(
                    $propertyName,
                    $value,
                    $inputProperties
                );
                break;
            case 'datetime':
                $inputProperties['data-format'] = 'YYYY-MM-DD\'T\'HH:mm:ss.SSS';
                $widget = new DateTimePicker(
                    $propertyName,
                    $value,
                    $inputProperties
                );
                break;
            case 'string':
                $widget = new \Ease\Html\InputTextTag(
                    $propertyName,
                    $value,
                    $inputProperties
                );
                break;
            default:
                $this->addStatusMessage(sprintf(
                    _('Unknown type of data %s'),
                    $type
                ), 'warning');
                $widget = new \Ease\Html\InputTag(
                    $propertyName,
                    $value,
                    $inputProperties
                );
                $note = '?: ' . $type;
                break;
        }


        if (is_array($note)) {
            $note[] = ' ' . _('Type') . ': ' . $type;
        } else {
            $note .= ' ' . _('Type') . ': ' . $type;
        }

        $this->addInput($widget, $propertyName . ': ' . $name, $placeholder, $note);
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
        if (isset($colProperties['values'])) {
            foreach ($colProperties['values']['value'] as $colValue) {
                $options[$colValue['@key']] = $colValue['$'];
            }
        }
        return $options;
    }

    /**
     * Add ExtIDs form
     */
    public function finalize()
    {
        parent::finalize();
        \Ease\JQuery\Part::jQueryze();
        $id = $this->engine->getDataValue('id');
        if (is_array($id)) {
            $id = current($id);
        }

        if ($id) {
            $contents = $this->pageParts;
            $this->emptyContents();

            $editorTabs = new \Ease\TWB5\Tabs('EditorTabs');
            $editorTabs->addTab(_('Columns'), $contents);
            $editorTabs->addTab(_('External IDs'), $this->extIDsEditor());
            $editorTabs->addTab(_('Labels'), new LabelSwitches($this->engine));
            $editorTabs->addTab(
                _('Query'),
                new SendForm(
                    $this->engine->getEvidenceURL(),
                    'PUT',
                    $this->engine->getJsonizedData($this->engine->getData())
                )
            );

            $editorTabs->addTab(
                _('AbraFlexi'),
                new \Ease\Html\IframeTag(
                    str_replace(
                        '.json',
                        '.html',
                        $this->engine->getEvidenceURL() . '/' . $id . '.' . $this->engine->format . '?inDesktopApp=true'
                    ),
                    ['style' => 'width: 100%; height: 600px', 'frameborder' => 0]
                )
            );

            $this->addItem($editorTabs);
        }
    }

    /**
     * External IDs editor
     *
     * @return \Ease\TWB5\Container
     */
    public function extIDsEditor()
    {
        $extIDsEditor = new \Ease\TWB5\Container(new \Ease\Html\InputHiddenTag(
            'id',
            $this->engine->getDataValue('id')
        ));
        $externalIDs = $this->engine->getDataValue('external-ids');
        if (count($externalIDs)) {
            foreach ($externalIDs as $externalID) {
                if (!strlen($externalID)) {
                    continue;
                }
                $idParts = explode(':', $externalID);
                if (!isset($idParts[2])) {
                    $idParts[2] = '';
                }

                $extIDrow = new \Ease\TWB5\Row();
                $extIDrow->addColumn(
                    4,
                    new \Ease\TWB5\Checkbox(
                        'deleteExtID[' . $idParts[1] . ']',
                        $externalID,
                        _('Remove')
                    )
                );
                $extIDrow->addColumn(
                    8,
                    new \Ease\TWB5\FormGroup(
                        $idParts[1],
                        new \Ease\Html\InputTextTag(
                            'external-ids[' . $idParts[1] . ']',
                            $idParts[2],
                            ['maxlength' => '20']
                        ),
                        $idParts[1],
                        $externalID
                    )
                );
                $extIDsEditor->addItem($extIDrow);
            }
        }

        $extIDsEditor->addItem(new \Ease\TWB5\FormGroup(
            _('New'),
            new \Ease\Html\InputTextTag('external-ids[]'),
            'ext:..',
            new \Ease\Html\ATag(
                'https://www.flexibee.eu/api/dokumentace/ref/identifiers/',
                _('External IDs')
            )
        ));

        $extIDsEditor->addItem(new \Ease\TWB5\SubmitButton(_('OK') . ' ' . new \Ease\TWB5\GlyphIcon('save')));
        return $extIDsEditor;
    }

    /**
     * Vraci kod pro ukladani policka formulare po editaci
     *
     * @param string $fieldName
     * @return string javascript
     */
    public function onChangeCode($fieldName)
    {
        $chCode = '';
        $id = $this->engine->getMyKey();
        if (!is_null($id)) {
            $chCode = 'saveColumnData(\'' . str_replace(
                '\\',
                '-',
                get_class($this->engine)
            ) . '\', \'' . $id . '\', \'' . $fieldName . '\', \'' . $this->engine->getEvidence() . '\')';
        }
        return $chCode;
    }
}
