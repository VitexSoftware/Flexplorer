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
     * Data Source Object
     * @var \FlexiPeeHP 
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


        $this->addItem(new TWBSwitch('toFlexiBee', false, 'on',
            ['onText' => _('Save to FlexiBee'), 'offText' => _('Show in editor')]));
        $this->addItem(new \Ease\TWB\SubmitButton(_('OK').' '.new \Ease\TWB\GlyphIcon('save')));
        $this->engine = $engine;
    }

    /**
     * Add an FlexiBee column type input field
     *
     * @param array $colProperties
     */
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
            $note .= '<span class="error">'._('Required').'</span> ';
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

                $widget   = new TWBSwitch($propertyName, $value, true,
                    $inputProperties);
                break;
            case 'relation':
                $evidence = '';
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
                        new \Ease\TWB\GlyphIcon('edit').' '._('Edit targeted record'));
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
                $this->addStatusMessage(sprintf(_('Unknown type of data %s'),
                        $type), 'warning');
                $widget                         = new \Ease\Html\InputTag($propertyName,
                    $value, $inputProperties);
                $note                           = '?: '.$type;
                break;
        }


        if (is_array($note)) {
            $note[] = ' '._('Type').': '.$type;
        } else {
            $note .= ' '._('Type').': '.$type;
        }

        $this->addInput($widget, $propertyName.': '.$name, $placeholder, $note);
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

        $id = $this->engine->getDataValue('id');
        if (is_array($id)) {
            $id = current($id);
        }

        if ($id) {
            $contents = $this->pageParts;
            $this->emptyContents();

            $editorTabs = new \Ease\TWB\Tabs('EditorTabs');
            $editorTabs->addTab(_('Columns'), $contents);
            $editorTabs->addTab(_('External IDs'), $this->extIDsEditor());
            $editorTabs->addTab(_('Labels'), new LabelSwitches($this->engine));
            $editorTabs->addTab(_('Query'),
                new SendForm($this->engine->getEvidenceURL(), 'PUT',
                $this->engine->jsonizeData($this->engine->getData())));

            $editorTabs->addTab(_('FlexiBee'),
                new \Ease\Html\IframeTag(str_replace('.json', '.html',
                    $this->engine->getEvidenceURL().'/'.$id.'.'.$this->engine->format.'?inDesktopApp=true'),
                ['style' => 'width: 100%; height: 600px', 'frameborder' => 0]));


            $this->addItem($editorTabs);
        }
    }

    /**
     * External IDs editor
     *
     * @return \Ease\TWB\Container
     */
    public function extIDsEditor()
    {
        $extIDsEditor = new \Ease\TWB\Container(new \Ease\Html\InputHiddenTag('id',
            $this->engine->getDataValue('id')));
        $externalIDs  = $this->engine->getDataValue('external-ids');
        if (count($externalIDs)) {
            foreach ($externalIDs as $externalID) {
                if (!strlen($externalID)) {
                    continue;
                }
                $idParts = explode(':', $externalID);
                if (!isset($idParts[2])) {
                    $idParts[2] = '';
                }

                $extIDrow = new \Ease\TWB\Row();
                $extIDrow->addColumn(4,
                    new \Ease\TWB\Checkbox('deleteExtID['.$idParts[1].']',
                    $externalID, _('Remove')));
                $extIDrow->addColumn(8,
                    new \Ease\TWB\FormGroup($idParts[1],
                    new \Ease\Html\InputTextTag('external-ids['.$idParts[1].']',
                    $idParts[2], ['maxlength' => '20']), $idParts[1],
                    $externalID));
                $extIDsEditor->addItem($extIDrow);
            }
        }

        $extIDsEditor->addItem(new \Ease\TWB\FormGroup(_('New'),
            new \Ease\Html\InputTextTag('external-ids[]'), 'ext:..',
            new \Ease\Html\ATag('https://www.flexibee.eu/api/dokumentace/ref/identifiers/',
            _('External IDs'))));

        $extIDsEditor->addItem(new \Ease\TWB\SubmitButton(_('OK').' '.new \Ease\TWB\GlyphIcon('save')));
        return $extIDsEditor;
    }
}
