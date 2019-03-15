<?php
/**
 * Flexplorer - vícesloupcový formulář.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class ColumnsForm extends \Ease\TWB\Form
{
    /**
     * Column width.
     *
     * @var int
     */
    public $colsize = 4;

    /**
     * Row.
     *
     * @var \Ease\TWB\Row
     */
    public $row = null;

    /**
     * Number of items in row.
     *
     * @var int
     */
    public $itemsPerRow = 4;

    /**
     * @var \Flexplorer\Flexplorer
     */
    public $engine = null;

    /**
     * Send buttons.
     *
     * @var \Ease\Html\Div
     */
    public $savers = null;

    /**
     * Bootstrapu form.
     *
     * @param SysEngine $engine        form contents source
     * @param mixed     $formContents  form contents
     * @param array     $tagProperties tag properties ex.:
     *                                 ['enctype' => 'multipart/form-data']
     */
    public function __construct($engine, $formContents = null,
                                $tagProperties = null)
    {
        $this->engine = $engine;
        parent::__construct(
            get_class($engine), '', 'POST', $formContents, $tagProperties
        );
        $this->newRow();
        $this->savers = new \Ease\Html\DivTag(null,
            ['style' => 'text-align: right']);
    }

    /**
     * Přidá další řadu formuláře.
     *
     * @return \Ease\TWB\Row Nově vložený řádek formuláře
     */
    public function newRow()
    {
        return $this->row = $this->addItem(new \Ease\TWB\Row());
    }

    /**
     * Vloží prvek do sloupce formuláře.
     *
     * @param mixed  $input       Vstupní prvek
     * @param string $caption     Popisek
     * @param string $placeholder předvysvětlující text
     * @param string $helptext    Dodatečná nápověda
     * @param string $addTagClass CSS třída kterou má být oskiován vložený prvek
     */
    public function addInput($input, $caption = null, $placeholder = null,
                             $helptext = null, $addTagClass = 'form-control')
    {
        if ($this->row->getItemsCount() > $this->itemsPerRow) {
            $this->row = $this->addItem(new \Ease\TWB\Row());
        }

        return $this->row->addItem(new \Ease\TWB\Col($this->colsize,
                new \Ease\TWB\FormGroup($caption, $input, $placeholder,
                $helptext, $addTagClass)));
    }

    /**
     * Přidá do formuláře tlačítko "Uložit".
     */
    public function addSubmitSave()
    {
        $this->savers->addItem(new \Ease\TWB\SubmitButton(_('Save'), 'default'),
            ['style' => 'text-align: right']);
    }

    /**
     * Přidá do formuláře tlačítko "Uložit a zpět na přehled".
     */
    public function addSubmitSaveAndList()
    {
        $this->savers->addItem(new \Ease\Html\InputSubmitTag('gotolist',
            _('Save & go to listing'), ['class' => 'btn btn-info']));
    }

    /**
     * Přidá do formuláře tlačítko "Uložit a další".
     */
    public function addSubmitSaveAndNext()
    {
        $this->savers->addItem(new \Ease\Html\InputSubmitTag('gotonew',
            _('Save and next'), ['class' => 'btn btn-success']));
    }

    /**
     * Vyplní formulář.
     *
     * @param SysEngine $datasource
     */
    public function populate($datasource)
    {
        $recordID = $datasource->getMyKey();

        foreach ($datasource->keywordsInfo as $col_name => $col_info) {
            if (isset($datasource->myLastModifiedColumn) && ($datasource->myLastModifiedColumn
                == $col_name)) {
                continue;
            }
            if (isset($datasource->myCreateColumn) && ($datasource->myCreateColumn
                == $col_name)) {
                continue;
            }
            $placeholder = $helptext    = '';
            $value       = $datasource->getDataValue($col_name);
            if (isset($col_info['title'])) {
                $caption = $col_info['title'];
            } else {
                $caption = $col_name;
            }
            if (isset($datasource->useKeywords[$col_name])) {
                $type = preg_replace('/[^A-Z]+/', '',
                    $datasource->useKeywords[$col_name]);
            } else {
                $type = 'text';
            }

            switch ($type) {
                case 'BOOL':
                    $input_widget = new \Ease\ui\TWBSwitch($col_name, $value);
                    break;
                case 'INT':
                    $input_widget = new \Ease\Html\InputTextTag($col_name,
                        $value, ['type' => 'number']);
                    break;
                case 'DATE':
                    $input_widget = new DateTimePicker($col_name, $value);
                    break;
                case 'TEXT':
                    $input_widget = new \Ease\Html\TextareaTag($col_name,
                        $value, ['class' => 'form-control']);
                    break;
                case 'STRING':
                    $input_widget = new \Ease\Html\InputTag($col_name, $value,
                        ['class' => 'form-control']);
                default:
                    break;
            }

            $this->addInput($input_widget, $caption, $placeholder, $helptext);
        }
    }

    public function finalize()
    {
        $recordID = $this->engine->getMyKey();
        $this->addItem(new \Ease\Html\InputHiddenTag('class',
            get_class($this->engine)));
        if (!is_null($recordID)) {
            $this->addItem(new \Ease\Html\InputHiddenTag($this->engine->keyColumn,
                $recordID));
        }
        $this->addItem($this->savers);
        \Ease\Shared::webPage()->includeJavaScript('js/jquery.validate.js');
        \Ease\Shared::webPage()->includeJavaScript('js/messages_cs.js');

        return parent::finalize();
    }
}