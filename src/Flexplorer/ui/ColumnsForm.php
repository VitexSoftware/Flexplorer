<?php

declare(strict_types=1);

/**
 * This file is part of the Flexplorer package
 *
 * github.com/VitexSoftware/Flexplorer
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flexplorer\ui;

class ColumnsForm extends \Ease\TWB5\Form
{
    /**
     * Column width.
     */
    public int $colsize = 4;

    /**
     * Row.
     */
    public \Ease\TWB5\Row $row = null;

    /**
     * Number of items in row.
     */
    public int $itemsPerRow = 4;
    public \Flexplorer\Flexplorer $engine = null;

    /**
     * Send buttons.
     */
    public \Ease\Html\Div $savers = null;

    /**
     * Bootstrapu form.
     *
     * @param SysEngine $engine        form contents source
     * @param mixed     $formContents  form contents
     * @param array     $tagProperties tag properties ex.:
     *                                 ['enctype' => 'multipart/form-data']
     */
    public function __construct(
        $engine,
        $formContents = null,
        $tagProperties = null,
    ) {
        $this->engine = $engine;
        parent::__construct(['name' => $engine::class, 'method' => 'POST'], $formContents, $tagProperties);
        $this->newRow();
        $this->savers = new \Ease\Html\DivTag(
            null,
            ['style' => 'text-align: right'],
        );
    }

    /**
     * Přidá další řadu formuláře.
     *
     * @return \Ease\TWB5\Row Nově vložený řádek formuláře
     */
    public function newRow()
    {
        return $this->row = $this->addItem(new \Ease\TWB5\Row());
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
    public function addInput(
        $input,
        $caption = null,
        $placeholder = null,
        $helptext = null,
        $addTagClass = 'form-control',
    ) {
        if ($this->row->getItemsCount() > $this->itemsPerRow) {
            $this->row = $this->addItem(new \Ease\TWB5\Row());
        }

        return $this->row->addItem(new \Ease\TWB5\Col(
            $this->colsize,
            new \Ease\TWB5\FormGroup(
                $caption,
                $input,
                $placeholder,
                $helptext,
                $addTagClass,
            ),
        ));
    }

    /**
     * Přidá do formuláře tlačítko "Uložit".
     */
    public function addSubmitSave(): void
    {
        $this->savers->addItem(
            new \Ease\TWB5\SubmitButton(_('Save'), 'default'),
            ['style' => 'text-align: right'],
        );
    }

    /**
     * Přidá do formuláře tlačítko "Uložit a zpět na přehled".
     */
    public function addSubmitSaveAndList(): void
    {
        $this->savers->addItem(new \Ease\Html\InputSubmitTag(
            'gotolist',
            _('Save & go to listing'),
            ['class' => 'btn btn-info'],
        ));
    }

    /**
     * Přidá do formuláře tlačítko "Uložit a další".
     */
    public function addSubmitSaveAndNext(): void
    {
        $this->savers->addItem(new \Ease\Html\InputSubmitTag(
            'gotonew',
            _('Save and next'),
            ['class' => 'btn btn-success'],
        ));
    }

    /**
     * Vyplní formulář.
     *
     * @param SysEngine $datasource
     */
    public function populate($datasource): void
    {
        $recordID = $datasource->getMyKey();

        foreach ($datasource->keywordsInfo as $col_name => $col_info) {
            if (isset($datasource->myLastModifiedColumn) && ($datasource->myLastModifiedColumn === $col_name)) {
                continue;
            }

            if (isset($datasource->myCreateColumn) && ($datasource->myCreateColumn === $col_name)) {
                continue;
            }

            $placeholder = $helptext = '';
            $value = $datasource->getDataValue($col_name);

            if (isset($col_info['title'])) {
                $caption = $col_info['title'];
            } else {
                $caption = $col_name;
            }

            if (isset($datasource->useKeywords[$col_name])) {
                $type = preg_replace(
                    '/[^A-Z]+/',
                    '',
                    $datasource->useKeywords[$col_name],
                );
            } else {
                $type = 'text';
            }

            switch ($type) {
                case 'BOOL':
                    $input_widget = new \Ease\TWB5\Widgets\TWBSwitch($col_name, $value);

                    break;
                case 'INT':
                    $input_widget = new \Ease\Html\InputTextTag(
                        $col_name,
                        $value,
                        ['type' => 'number'],
                    );

                    break;
                case 'DATE':
                    $input_widget = new DateTimePicker($col_name, $value);

                    break;
                case 'TEXT':
                    $input_widget = new \Ease\Html\TextareaTag(
                        $col_name,
                        $value,
                        ['class' => 'form-control'],
                    );

                    break;
                case 'STRING':
                    $input_widget = new \Ease\Html\InputTag(
                        $col_name,
                        $value,
                        ['class' => 'form-control'],
                    );

                    // no break
                default:
                    break;
            }

            $this->addInput($input_widget, $caption, $placeholder, $helptext);
        }
    }

    public function finalize()
    {
        $recordID = $this->engine->getMyKey();
        $this->addItem(new \Ease\Html\InputHiddenTag(
            'class',
            \get_class($this->engine),
        ));

        if (null !== $recordID) {
            $this->addItem(new \Ease\Html\InputHiddenTag(
                $this->engine->keyColumn,
                $recordID,
            ));
        }

        $this->addItem($this->savers);
        WebPage::singleton()->includeJavaScript('js/jquery.validate.js');
        WebPage::singleton()->includeJavaScript('js/messages_cs.js');

        return parent::finalize();
    }
}
