<?php
/**
 * Flexplorer - Datový adaptér.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

class Flexplorer extends \FlexiPeeHP\FlexiBeeRW
{
    /**
     * @var array Pole HTTP hlaviček odesílaných s každým požadavkem
     */
    public $defaultHttpHeaders = ['User-Agent' => 'Flexplorer 0.1 (using FlexiPeeHP library)'];

    /**
     * Struktura evidence
     * @var array
     */
    public $evidenceStructure = [];

    /**
     *
     * @param type $evidence
     */
    public function __construct($evidence)
    {
        $this->setEvidence($evidence);
        parent::__construct();
        $this->evidenceStructure = $this->getColumnsInfo();
    }

    public function getColumnsInfo()
    {
        $structFile = sys_get_temp_dir().'/flexplorer-'.$this->evidence.'.struct';
        if (file_exists($structFile)) {
            $useKeywords = unserialize(file_get_contents($structFile));
        } else {
            $useKeywords = [];
            $flexinfo    = $this->performRequest($this->evidence.'/properties.json');
            if (count($flexinfo)) {
                foreach ($flexinfo['properties']['property'] as $evidenceProperty) {
                    $key                       = $evidenceProperty['propertyName'];
                    $useKeywords[$key]         = $evidenceProperty;
                    $useKeywords[$key]['name'] = $evidenceProperty['name'];
                    $useKeywords[$key]['type'] = $evidenceProperty['type'];
                }
            }

            file_put_contents($structFile, serialize($useKeywords));
        }
        return $useKeywords;
    }

    /**
     * Vrací všechny záznamy jako html
     * @param array $data
     * @return array
     */
    public function htmlizeData($data)
    {
        if (is_array($data) && count($data)) {
            $usedCache = [];
            foreach ($data as $rowId => $row) {
                $htmlized = $this->htmlizeRow($row);

                if (is_array($htmlized)) {
                    foreach ($htmlized as $key => $value) {
                        if (!is_null($value)) {
                            $data[$rowId][$key] = $value;
                        } else {
                            if (!isset($data[$rowId][$key])) {
                                $data[$rowId][$key] = $value;
                            }
                        }
                    }
                    if (isset($row['register']) && ($row['register'] == 1)) {
                        $data[$rowId]['name'] = '';
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Vrací řádek dat v HTML interpretaci
     *
     * @param array $row
     * @return array
     */
    public function htmlizeRow($row)
    {
        if (is_array($row) && count($row)) {
            foreach ($row as $key => $value) {
                $fieldType = 'STRING';

                if (isset($this->evidenceStructure[$key]['type']) && !strstr($key,
                        '@')) {
                    $fieldType = $this->evidenceStructure[$key]['type'];
                }

                $fType = strtoupper(preg_replace('/\(.*\)/', '', $fieldType));
                switch ($fType) {
                    case 'LOGIC':
                    case 'BOOL':
                        if (is_null($value) || !strlen($value)) {
                            $row[$key] = '<em>NULL</em>';
                        } else {
                            if (($value === '0') || ($value === 'false')) {
                                $row[$key] = \Ease\TWB\Part::glyphIcon('unchecked')->__toString();
                            } else {
                                if (($value === '1') || ($value === 'true')) {
                                    $row[$key] = \Ease\TWB\Part::glyphIcon('check')->__toString();
                                }
                            }
                        }
                        break;
                    case 'RELATION':
                        if (isset($this->evidenceStructure[$key]['url'])) {
                            $tmp       = explode('/',
                                $this->evidenceStructure[$key]['url']);
                            $revidence = 'evidence.php?evidence='.end($tmp);
                            $row[$key] = '<a href="'.$revidence.'">'.\Ease\TWB\Part::glyphIcon('link',
                                    ['title' => $this->evidenceStructure[$key]['fkName']])->__toString().'</a> '.$value;
                        }
                        break;
                    case 'SELECT':

                        break;
                    case 'IDLIST':
                        if (!is_array($value) && strlen($value)) {
                            if (strstr($value, ':{')) {
                                $values = unserialize(stripslashes($value));
                            } else {
                                $values = ['0' => $value];
                            }
                            if (!is_array($values)) {
                                $this->addStatusMessage(sprintf(_('Chyba unserializace %s #%s '),
                                        $value, $key));
                            }
                            if (isset($this->keywordsInfo[$key]['refdata'])) {
                                $idcolumn     = $this->keywordsInfo[$key]['refdata']['idcolumn'];
                                $table        = $this->keywordsInfo[$key]['refdata']['table'];
                                $searchColumn = $this->keywordsInfo[$key]['refdata']['captioncolumn'];
                                $target       = str_replace('_id', '.php',
                                    $idcolumn);
                                foreach ($values as $id => $name) {
                                    if ($id) {
                                        $values[$id] = '<a title="'.$table.'" href="'.$target.'?'.$idcolumn.'='.$id.'">'.$name.'</a>';
                                    } else {
                                        $values[$id] = '<a title="'.$table.'" href="search.php?search='.$name.'&table='.$table.'&column='.$searchColumn.'">'.$name.'</a> '.\Ease\TWB\Part::glyphIcon('search');
                                    }
                                }
                            }
                            $value     = implode(',', $values);
                            $row[$key] = $value;
                        }
                        break;
                    default :
                        if (isset($this->keywordsInfo[$key]['refdata']) && strlen(trim($value))) {
                            $table        = $this->keywordsInfo[$key]['refdata']['table'];
                            $searchColumn = $this->keywordsInfo[$key]['refdata']['captioncolumn'];
                            $row[$key]    = '<a title="'.$table.'" href="search.php?search='.$value.'&table='.$table.'&column='.$searchColumn.'">'.$value.'</a> '.\Ease\TWB\Part::glyphIcon('search');
                        }
                        if (strstr($key, 'url')) {
                            $row[$key] = '<a href="'.$value.'">'.$value.'</a>';
                        }

                        break;
                }
            }
        }
        return $row;
    }

    /**
     * Vrací vyhledávací výraz pro řetězec
     *
     * @param string $what co hledat
     * @return string vyhledávací výraz
     */
    public function searchString($what)
    {
        $query = '';
        $conds = [];
        foreach ($this->evidenceStructure as $columnInfo) {
            if ($columnInfo['type'] === 'string') {
                if ($columnInfo['propertyName'] == 'stitky') {
                    continue;
                }
                if ($columnInfo['propertyName'] == 'faNazev2') {
                    continue;
                }

                $conds[$columnInfo['propertyName']] = $columnInfo['propertyName']." like '".$what."'";
            }
        }
        return $this->getColumnsFromFlexibee(array_keys($conds),
                implode(' or ', $conds));
    }

    /**
     * Převezme pouze známe sloupečky
     *
     * @param array $data vstupní data
     * @return array políčka evidence
     */
    public function takeData($data)
    {
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $this->evidenceStructure)) {
                unset($data[$key]);
            }
        }
        return parent::takeData($data);
    }
}