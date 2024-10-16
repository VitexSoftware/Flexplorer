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

/**
 * Description of DataGrid.
 *
 * @author vitex
 */
class DataGrid extends \Ease\Html\TableTag
{
    /**
     * Extra filtr výsledků.
     */
    public string $select;

    /**
     * Default column settings.
     */
    public array $defaultColProp = ['sortable' => true];

    /**
     * Options.
     */
    public array $options = [
        'method' => 'GET',
        'dataType' => 'json',
        'height' => 'auto',
        'width' => 'auto',
        'sortname' => 'id',
        'sortorder' => 'asc',
        'usepager' => true,
        'useRp' => true,
        'rp' => 20,
        'dblClickResize' => true,
        'showTableToggleBtn' => true,
        'add' => [],
        'edit' => [],
        //        'buttons' => [
        //            ['name' => 'CSV Export', 'bclass' => 'csvexport']
        //        , array('name' => 'PDF Export', 'bclass' => 'pdfexport')
        //        ]
    ];
    public $addFormItems = [['name' => 'action', 'value' => 'add', 'type' => 'hidden']];
    public $editFormItems = [['name' => 'action', 'value' => 'edit', 'type' => 'hidden']];

    /**
     * Objekt jehož data jsou zobrazována.
     */
    public \Flexplorer\DataSource $dataSource = null;

    /**
     * Klik na řádku vede na editor záznamu.
     */
    public type $dblclk2edit = true;

    /**
     * Zdroj dat pro flexigrid.
     *
     * @param string $name       ID elementu
     * @param string $datasource URL
     * @param array  $properties vlastnosti elementu
     */
    public function __construct($name, $datasource, $properties = null)
    {
        $this->dataSource = $datasource;
        $this->options['title'] = $name;
        $this->setTagID();

        $this->options['url'] = 'datasource.php?evidence='.urlencode($datasource->getEvidence());

        if (isset($properties['label'])) {
            $this->options['url'] .= '&stitek='.urlencode($properties['label']);
        }

        $this->options['sortname'] = $datasource->getKeyColumn();
        $dataurl = null;

        parent::__construct($dataurl, $properties);
        \Ease\Part::jQueryze($this);
        WebPage::singleton()->includeJavaScript('js/flexigrid.js');
        WebPage::singleton()->includeCSS('css/flexigrid.css');
        $this->setUpButtons();
        $this->setUpColumns();
    }

    /**
     * Set up DataGrid buttons.
     */
    public function setUpButtons(): void
    {
        $this->addJsonButton(_('Json'));
        $this->addXmlButton(_('XML'));
        $this->addPdfButton(_('PDF'));
        $actions = $this->dataSource->handledObejct->getActionsInfo();

        if (\count($actions)) {
            foreach ($actions as $action => $actionInfo) {
                switch ($action) {
                    case 'new':
                    case 'add':
                        $this->addAddButton(_('Add'));

                        break;
                    case 'edit':
                        $this->addEditButton(_('Edit'));

                        break;
                    case 'delete':
                        $this->addDeleteButton(_('Delete'));

                        break;

                    default:
                        $this->addActionButton(
                            $actionInfo['actionName'],
                            $action,
                        );

                        break;
                }
            }
        }

        $this->addSelectAllButton(_('Select All'));
    }

    /**
     * Add an Action button.
     *
     * @param string $title  Buttin title
     * @param string $action AbraFlexi action
     */
    public function addActionButton($title, $action): void
    {
        $show = false;

        $actionFunction = str_replace('-', '_', $action);

        $this->addButton($title, $action, 'action'.$actionFunction);

        $this->addJavaScript('function action'.$actionFunction.<<<'EOD'
(com, grid) {
              var action = $("div span" ,this).attr("class");

                var numItems = $('.trSelected').length
                if(numItems){
                    if(numItems == 1) {
                        $('.trSelected', grid).each(function() {
                            var id = $(this).attr('id');
                            id = id.substring(id.lastIndexOf('row')+3);
                            $(location).attr('href','query.php?show=result&evidence=
EOD.$this->dataSource->getEvidence().'&action=\' + action + \'&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' +id);
                        });

                    } else {
                        $('.trSelected', grid).each(function() {
                            var id = $(this).attr('id');
                            id = id.substring(id.lastIndexOf('row')+3);
                            var url ='query.php?show=result&evidence=
EOD.$this->dataSource->getEvidence().'&action=\' + action + \'&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' +id;
                            var win = window.open(url, '_blank');
                            win.focus();
                        });
                    }
                } else {
                      $(location).attr('href','evidence.php?evidence=
EOD.$this->dataSource->getEvidence().<<<'EOD'
&action=' + action);
                }

            }

EOD, null, true);
    }

    /**
     * Nastaví vlastností sloupečků datagridu.
     */
    public function setUpColumns(): void
    {
        foreach ($this->dataSource->keywordsInfo as $keyword => $properties) {
            $colProperties = [];
            $type = $properties['type'];

            if ($properties['isSortable'] === 'true') {
                $colProperties['sortable'] = 'true';
            } else {
                $colProperties['sortable'] = 'false';
            }

            if (!isset($this->dataSource->keywordsInfo[$keyword]['title']) || !\strlen(trim($this->dataSource->keywordsInfo[$keyword]['title']))) {
                $this->addStatusMessage(
                    _('Title missing').' '.$this->dataSource->keyword.': '.$keyword,
                    'warning',
                );
                $this->dataSource->keywordsInfo[$keyword]['title'] = $keyword;
            }

            $this->setColumn(
                $keyword,
                $this->dataSource->keywordsInfo[$keyword]['title'],
                true,
                $colProperties,
            );
        }
    }

    /**
     * Přidá tlačítko.
     *
     * @param string     $title   Popisek tlačítka
     * @param string     $class   CSS třída tlačítka
     * @param null|mixed $onpress
     */
    public function addButton($title, $class, $onpress = null): void
    {
        if ($onpress) {
            $this->options['buttons'][] = ['name' => $title, 'bclass' => $class,
                'onpress: '.$onpress];
        } else {
            $this->options['buttons'][] = ['name' => $title, 'bclass' => $class];
        }
    }

    /**
     * Vloží přidávací tlačítko.
     *
     * @param string $title  Nadpis gridu
     * @param string $target Url
     */
    public function addAddButton($title, $target = null): void
    {
        $show = false;

        if (null === $target) {
            $target = $this->options['url'];
        }

        $this->addButton($title, 'add', 'addRecord');

        $this->addCSS(<<<'EOD'
.flexigrid div.fbutton .add {
background: url(images/add.png) no-repeat center left;
}
EOD);

        $this->addJavaScript(<<<'EOD'
function addRecord(com, grid) {
              $(location).attr('href','editor.php?evidence=
EOD.$this->dataSource->getEvidence().<<<'EOD'
');
            }

EOD, null, true);
    }

    /**
     * Vloží tlačítko výběru všech zobrazených záznamů.
     *
     * @param type $title
     * @param type $target
     */
    public function addSelectAllButton($title, $target = null): void
    {
        $this->addButton($title, 'selectAll', 'selectAll');
        $this->addJavaScript(<<<'EOD'
function selectAll(com, grid) {
                $('tr', grid).each(function() {
                    $(this).click();
                });
}
EOD);
    }

    /**
     * Vloží editační tlačítko.
     *
     * @param type $title
     * @param type $target
     */
    public function addEditButton($title, $target = null): void
    {
        $this->addButton($title, 'edit', 'editRecord');

        $this->addCss(<<<'EOD'
.flexigrid div.fbutton .edit {
background: url(images/edit.png) no-repeat center left;
}

EOD);

        $this->addJavaScript(<<<'EOD'
function editRecord(com, grid) {

        var numItems = $('.trSelected').length
        if(numItems){
            if(numItems == 1) {
                $('.trSelected', grid).each(function() {
                    var id = $(this).attr('id');
                    id = id.substring(id.lastIndexOf('row')+3);
                    $(location).attr('href','editor.php?evidence=
EOD.$this->dataSource->getEvidence().'&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' +id);
                });

            } else {
                $('.trSelected', grid).each(function() {
                    var id = $(this).attr('id');
                    id = id.substring(id.lastIndexOf('row')+3);
                    var url ='editor.php?evidence=
EOD.$this->dataSource->getEvidence().'&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' +id;
                    var win = window.open(url, '_blank');
                    win.focus();
                });
            }
        } else {
            alert("
EOD._('Please mark some rows').<<<'EOD'
");
        }

            }

EOD, null, true);
    }

    public function addXmlButton($title, $target = null): void
    {
        $this->addButton($title, 'xml', 'xmlRecord');

        $this->addCss(<<<'EOD'
.flexigrid div.fbutton .xml {
background: url(images/xml.svg) no-repeat center left;
}

EOD);

        $this->addJavaScript(<<<'EOD'
function xmlRecord(com, grid) {

        var numItems = $('.trSelected').length
        if(numItems){
            if(numItems == 1) {
                $('.trSelected', grid).each(function() {
                    var id = $(this).attr('id');
                    id = id.substring(id.lastIndexOf('row')+3);
                    $(location).attr('href','query.php?format=xml&show=result&evidence=
EOD.$this->dataSource->getEvidence().'&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' +id);
                });

            } else {
                var ids = [];
                $('.trSelected', grid).each(function() {
                    var id = $(this).attr('id');
                    id = id.substring(id.lastIndexOf('row')+3);
                    ids.push( id );
                });
                $(location).attr('href','query.php?format=xml&show=result&evidence=
EOD.$this->dataSource->getEvidence().'&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' + ids.join());
            }
        } else {
            alert("
EOD._('Please mark some rows').<<<'EOD'
");
        }

            }

EOD, null, true);
    }

    public function addJsonButton($title, $target = null): void
    {
        $this->addButton($title, 'json', 'jsonRecord');

        $this->addCss(<<<'EOD'
.flexigrid div.fbutton .json {
background: url(images/json.svg) no-repeat center left;
}

EOD);

        $this->addJavaScript(<<<'EOD'
function jsonRecord(com, grid) {

        var numItems = $('.trSelected').length
        if(numItems){
            if(numItems == 1) {
                $('.trSelected', grid).each(function() {
                    var id = $(this).attr('id');
                    id = id.substring(id.lastIndexOf('row')+3);
                    $(location).attr('href','query.php?format=json&show=result&evidence=
EOD.$this->dataSource->getEvidence().'&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' +id);
                });

            } else {
                var ids = [];
                $('.trSelected', grid).each(function() {
                    var id = $(this).attr('id');
                    id = id.substring(id.lastIndexOf('row')+3);
                    ids.push( id );
                });
                $(location).attr('href','query.php?format=json&show=result&evidence=
EOD.$this->dataSource->getEvidence().'&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' + ids.join());
            }
        } else {
            alert("
EOD._('Please mark some rows').<<<'EOD'
");
        }

            }

EOD, null, true);
    }

    public function addPdfButton($title, $target = null): void
    {
        $this->addButton($title, 'pdf', 'pdfRecord');

        $this->addCss(<<<'EOD'
.flexigrid div.fbutton .pdf {
background: url(images/pdf.svg) no-repeat center left;
}

EOD);

        $this->addJavaScript(<<<'EOD'
function pdfRecord(com, grid) {

        var numItems = $('.trSelected').length
        if(numItems){
            if(numItems == 1) {
                $('.trSelected', grid).each(function() {
                    var id = $(this).attr('id');
                    id = id.substring(id.lastIndexOf('row')+3);
                    $(location).attr('href','document.php?format=pdf&show=result&evidence=
EOD.$this->dataSource->getEvidence().'&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' +id);
                });

            } else {
                var ids = [];
                $('.trSelected', grid).each(function() {
                    var id = $(this).attr('id');
                    id = id.substring(id.lastIndexOf('row')+3);
                    ids.push( id );
                });
                $(location).attr('href','document.php?format=pdf&show=result&evidence=
EOD.$this->dataSource->getEvidence().'&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' + ids.join());
            }
        } else {
            alert("
EOD._('Please mark some rows').<<<'EOD'
");
        }

            }

EOD, null, true);
    }

    /**
     * Přidá tlačítko pro smazání záznamu.
     *
     * @param string $title  popisek tlačítka
     * @param string $target výkonný skript
     */
    public function addDeleteButton($title, $target = null): void
    {
        if (null === $target) {
            $target = $this->options['url'];
        }

        $this->addButton($title, 'delete', 'deleteRecord');

        $this->addCss(<<<'EOD'
.flexigrid div.fbutton .delete {
background: url(images/delete.png) no-repeat center left;
}
EOD);

        $this->addJavaScript(<<<'EOD'
function deleteRecord(com, grid) {

        var numItems = $('.trSelected').length
        if(numItems){
            if(numItems == 1) {
                $('.trSelected', grid).each(function() {
                    var id = $(this).attr('id');
                    id = id.substring(id.lastIndexOf('row')+3);
                    $(location).attr('href','delete.php?evidence=
EOD.$this->dataSource->getEvidence().'&action=delete&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' +id);
                });

            } else {
                $('.trSelected', grid).each(function() {
                    var id = $(this).attr('id');
                    id = id.substring(id.lastIndexOf('row')+3);
                    var url ='delete.php?evidence=
EOD.$this->dataSource->getEvidence().'&action=delete&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' +id;
                    var win = window.open(url, '_blank');
                    win.focus();
                });
            }
        } else {
            alert("
EOD._('Please mark some rows').<<<'EOD'
");
        }

            }

EOD, null, true);
    }

    /**
     * Nastaví parametry sloupečky.
     *
     * @param string $name             jméno z databáze
     * @param string $title            popisek sloupce
     * @param bool   $search           nabídnout pro sloupec vyhledávání
     * @param array  $columnProperties další vlastnosti v poli
     */
    public function setColumn(
        $name,
        $title,
        $search = false,
        $columnProperties = null
    ): void {
        if (!isset($this->options['colModel'])) {
            $this->options['colModel'] = [];
        }

        if (!isset($columnProperties['editable'])) {
            $columnProperties['editable'] = false;
        }

        $properties = $this->defaultColProp;
        $properties['name'] = $name;
        $properties['display'] = $title;

        if (\is_array($columnProperties)) {
            $this->options['colModel'][] = array_merge(
                $columnProperties,
                $properties,
            );
        } else {
            $this->options['colModel'][] = $properties;
        }

        if ($search) {
            if (\is_array($search)) {
                foreach ($search as $sid => $srch) {
                    $search[$sid] .= ' LIKE "%"';
                }

                $search = implode(' OR ', $search);
            }

            $this->options['searchitems'][] = ['display' => $title, 'name' => $name,
                'where' => addslashes($search)];
        }

        if ($columnProperties['editable']) {
            if (!isset($columnProperties['label'])) {
                $columnProperties['label'] = $title;
            }

            if (!isset($columnProperties['value'])) {
                $columnProperties['value'] = WebPage::singleton()->getRequestValue($name);
            }

            $columnProperties['name'] = $name;
            $this->editFormItems[$name] = $columnProperties;
            $this->addFormItems[$name] = $columnProperties;
        }
    }

    /**
     * Vložení skriptu.
     */
    public function finalize(): void
    {
        $grid_id = $this->getTagID();

        if ($this->getTagProperty('columnsAutoSize')) {
            $this->options['onSuccess'] = 'function() { addGrid($("#'.$grid_id.'"), this)}';
            // Patch Grid Responisive
            $grid_js = <<<'EOD'

        var grids=[];
            $(window).resize(function() {
                //Resize all the grids on the page
                //Only resize the ones whoes size has actually changed...
                for(var i in grids) {
                    if(grids[i].width!=grids[i].$grid.width()) {
                        sizeGrid(grids[i]);
                    }
                }
            });
EOD;
            $grid_js .= <<<'EOD'

            //Keep track of all grid elements and current sizes
            public function addGrid($table, grid) {
                var $grid = $table.closest('.flexigrid');
                var data = {$table:$table, $grid:$grid, grid:grid, width:$grid.width()};
                grids.push(data);
                sizeGrid(data);
            }
EOD;
            $grid_js .= <<<'EOD'

            //Make all cols with auto size fill remaining width..
            public function sizeGrid(data) {
                //Auto size the middle col.
                var totalWidth = data.$grid.outerWidth()-15; //15 padding - not found where this is set

                var fixedWidth = 0;
                var fluidCols = [];
                for(var i=0; i<data.grid.colModel.length; i++ ) {
                    if( !isNaN(data.grid.colModel[i].width) ) {
                        fixedWidth+=data.$table.find('tr:eq('+i+') td:eq('+i+'):visible').outerWidth(true);
                    } else {
                        fluidCols.push(i);
                    }
                }

                var newWidth = (totalWidth-fixedWidth)/fluidCols.length;
                for(var i in fluidCols) {
                    data.grid.g.colresize = { n:fluidCols[i], nw:newWidth };
                    data.grid.g.dragEnd( );
                }

                data.width = data.$grid.width();
            }
EOD;
        } else {
            $grid_js = '';
        }

        if ($this->select) {
            $this->options['query'] = current($this->select);
            $this->options['qtype'] = key($this->select);
        }

        if ($this->dblclk2edit) {
            $this->options['onDoubleClick'] = <<<'EOD'
function(g) {
                    var id = $(g).attr('id');
                    id = id.substring(id.lastIndexOf('row')+3);
                    $(location).attr('href','editor.php?evidence=
EOD.$this->dataSource->getEvidence().'&'.$this->dataSource->getKeyColumn().<<<'EOD'
=' +id);

            }
EOD;
        }

        $this->options['getGridClass'] = 'function(g) { this.g=g; return g; }';
        WebPage::singleton()->addJavaScript(
            "\n"
                .'$(\'#'.$grid_id.'\').flexigrid({ '.\Ease\Part::partPropertiesToString($this->options).' }); '.$grid_js,
            null,
            true,
        );
    }
}
