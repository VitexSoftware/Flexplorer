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

namespace Flexplorer;

/**
 * Flexplorer main data handling class.
 */
class Flexplorer extends \AbraFlexi\RW
{
    /**
     * @var array Pole HTTP hlaviček odesílaných s každým požadavkem
     */
    public array $defaultHttpHeaders = ['User-Agent' => 'Flexplorer 0.5 (using AbraFlexi library)'];

    /**
     * Struktura evidence.
     */
    public array $evidenceStructure = [];

    /**
     * @param type $evidence
     */
    public function __construct($evidence = null)
    {
        if (null === $evidence) {
            $evidence = $this->getEvidence();
        }

        if (null !== $evidence) {
            $this->setEvidence($evidence);
        }

        parent::__construct();
        $this->evidenceStructure = $this->getColumnsInfo();
    }

    /**
     * Add Default Url params to given url if not overrided.
     *
     * @deprecated since version 1.0
     *
     * @param string $urlRaw
     *
     * @return string url with default params added
     */
    public function addDefaultUrlParams($urlRaw)
    {
        return \Ease\Functions::addUrlParams($urlRaw, $this->defaultUrlParams);
    }

    /**
     * Return all records as html].
     *
     * @param array $data
     *
     * @return array
     */
    public function htmlizeData($data)
    {
        if (\is_array($data) && \count($data)) {
            foreach ($data as $rowId => $row) {
                $htmlized = $this->htmlizeRow($row);

                if (\is_array($htmlized)) {
                    foreach ($htmlized as $key => $value) {
                        if (null !== $value) {
                            if ($key === 'stitky') {
                                $data[$rowId][$key] = '';

                                foreach (\is_string($value) ? \AbraFlexi\Stitek::listToArray($value) : $value as $stitek) {
                                    if (\strlen($stitek)) {
                                        $data[$rowId][$key] .= ' <a href="listbylabel.php?label='.$stitek.'">'.$stitek.'</a> ';
                                    }
                                }
                            } else {
                                $data[$rowId][$key] = $value;
                            }
                        } else {
                            if (!isset($data[$rowId][$key])) {
                                $data[$rowId][$key] = $value;
                            }
                        }
                    }

                    if (isset($row['register']) && ($row['register'] === 1)) {
                        $data[$rowId]['name'] = '';
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Vrací řádek dat v HTML interpretaci.
     *
     * @param array $row
     *
     * @return array
     */
    public function htmlizeRow($row)
    {
        if (\is_array($row) && \count($row)) {
            foreach ($row as $key => $value) {
                $fieldType = 'STRING';

                if (
                    isset($this->evidenceStructure[$key]['type']) && !strstr(
                        $key,
                        '@',
                    )
                ) {
                    $fieldType = $this->evidenceStructure[$key]['type'];
                }

                $fType = strtoupper(preg_replace('/\(.*\)/', '', $fieldType));

                switch ($fType) {
                    case 'LOGIC':
                    case 'BOOL':
                        if (null === $value || !\strlen($value)) {
                            $row[$key] = '<em>NULL</em>';
                        } else {
                            if (($value === '0') || ($value === 'false')) {
                                $row[$key] = \Ease\TWB5\Part::glyphIcon('unchecked')->__toString();
                            } else {
                                if (($value === '1') || ($value === 'true')) {
                                    $row[$key] = \Ease\TWB5\Part::glyphIcon('check')->__toString();
                                }
                            }
                        }

                        break;
                    case 'RELATION':
                        if (isset($this->evidenceStructure[$key]['url'])) {
                            $tmp = explode(
                                '/',
                                $this->evidenceStructure[$key]['url'],
                            );
                            $revidence = 'evidence.php?evidence='.end($tmp);
                            $row[$key] = '<a href="'.$revidence.'">'.\Ease\TWB5\Part::glyphIcon(
                                'link',
                                ['title' => $this->evidenceStructure[$key]['fkName']],
                            )->__toString().'</a> '.$value;
                        }

                        break;
                    case 'SELECT':
                        break;
                    case 'IDLIST':
                        if (!\is_array($value) && \strlen($value)) {
                            if (strstr($value, ':{')) {
                                $values = unserialize(stripslashes($value));
                            } else {
                                $values = ['0' => $value];
                            }

                            if (!\is_array($values)) {
                                $this->addStatusMessage(sprintf(
                                    _('Unserialization error %s #%s '),
                                    $value,
                                    $key,
                                ));
                            }

                            if (isset($this->keywordsInfo[$key]['refdata'])) {
                                $idcolumn = $this->keywordsInfo[$key]['refdata']['idcolumn'];
                                $table = $this->keywordsInfo[$key]['refdata']['table'];
                                $searchColumn = $this->keywordsInfo[$key]['refdata']['captioncolumn'];
                                $target = str_replace(
                                    '_id',
                                    '.php',
                                    $idcolumn,
                                );

                                foreach ($values as $id => $name) {
                                    if ($id) {
                                        $values[$id] = '<a title="'.$table.'" href="'.$target.'?'.$idcolumn.'='.$id.'">'.$name.'</a>';
                                    } else {
                                        $values[$id] = '<a title="'.$table.'" href="search.php?search='.$name.'&table='.$table.'&column='.$searchColumn.'">'.$name.'</a> '.\Ease\TWB5\Part::glyphIcon('search');
                                    }
                                }
                            }

                            $value = implode(',', $values);
                            $row[$key] = $value;
                        }

                        break;

                    default:
                        if (isset($this->keywordsInfo[$key]['refdata']) && \strlen(trim($value))) {
                            $table = $this->keywordsInfo[$key]['refdata']['table'];
                            $searchColumn = $this->keywordsInfo[$key]['refdata']['captioncolumn'];
                            $row[$key] = '<a title="'.$table.'" href="search.php?search='.$value.'&table='.$table.'&column='.$searchColumn.'">'.$value.'</a> '.\Ease\TWB5\Part::glyphIcon('search');
                        }

                        if (strstr($key, 'url')) {
                            $row[$key] = '<a href="'.$value.'">'.$value.'</a>';
                        }

                        break;
                }
            }
        }

        $row['external-ids'] = isset($row['external-ids']) ? implode(
            ',',
            $row['external-ids'],
        ) : '';

        return $row;
    }

    /**
     * Vrací vyhledávací výraz pro řetězec.
     *
     * @param string $what co hledat
     *
     * @return string vyhledávací výraz
     */
    public function searchString($what)
    {
        $query = '';
        $conds = [];

        foreach ($this->evidenceStructure as $columnInfo) {
            if ($columnInfo['type'] === 'string') {
                if ($columnInfo['propertyName'] === 'stitky') {
                    continue;
                }

                if ($columnInfo['propertyName'] === 'faNazev2') {
                    continue;
                }

                $conds[$columnInfo['propertyName']] = $columnInfo['propertyName']." like '".$what."'";
            }
        }

        return $this->getColumnsFromAbraFlexi(
            array_keys($conds),
            implode(' or ', $conds),
        );
    }

    /**
     * Převezme pouze známe sloupečky.
     *
     * @param array $data vstupní data
     *
     * @return array políčka evidence
     */
    public function takeData(array $data): int
    {
        $fbColumns = $this->getColumnsInfo();
        $relations = $this->getRelationsInfo();

        if ($relations) {
            foreach ($relations as $relation) {
                if (\is_array($relation) && isset($relation['url'])) {
                    $fbRelations[$relation['url']] = $relation['url'];
                }
            }
        }

        if (\count($fbColumns)) {
            foreach ($data as $key => $value) {
                if ($key === 'external-ids') {
                    continue;
                }

                if (strstr($key, '@')) {
                    continue;
                }

                if (!\array_key_exists($key, $fbColumns)) {
                    if (!\array_key_exists($key, $fbRelations)) {
                        $this->addStatusMessage(sprintf(
                            'unknown column %s for evidence %s',
                            $key,
                            $this->getEvidence(),
                        ), 'warning');
                    } else {
                        if (!\is_array($value)) {
                            $this->addStatusMessage(sprintf(
                                'subevidence %s in evidence %s must bee an array',
                                $key,
                                $this->getEvidence(),
                            ), 'warning');
                        }
                    }
                }
            }
        }

        return parent::takeData($data);
    }

    /**
     * Checks to see of a string contains a particular substring.
     *
     * @param $substring the substring to match
     * @param $string    the string to search
     *
     * @return bool true if $substring is found in $string, false otherwise
     */
    public static function contains($substring, $string)
    {
        $found = true;
        $pos = strpos(strtolower($string), strtolower($substring));

        if ($pos === false) {
            $found = false;
        }

        return $found;
    }

    /**
     * @param type $originalIDs
     */
    public function changeExternalIDs($originalIDs): void
    {
        $extidToRemove = [];

        foreach ($this->getDataValue('external-id') as $extid) {
            if (!array_search($extid, $originalIDs, true)) {
                $extidToRemove[] = $extid;
            }
        }

        if (\count($extidToRemove)) {
            $this->setDataValue(
                '@removeExternalIds',
                implode(',', $extidToRemove),
            );
        }
    }

    /**
     * Interact with AbraFlexi.
     */
    public function performQuery()
    {
        $webPage = ui\WebPage::singleton();

        $id = $webPage->getRequestValue('id');
        $url = $webPage->getRequestValue('url');
        $body = urldecode($webPage->getRequestValue('body'));
        $action = $webPage->getRequestValue('action');
        $method = $webPage->getRequestValue('method');
        $format = $webPage->getRequestValue('format');
        $sourceurl = $webPage->getRequestValue('sourceurl');

        if (isset($_FILES['upload']) && \strlen($_FILES['upload']['tmp_name'])) {
            $body = file_get_contents($_FILES['upload']['tmp_name']);
            $this->addStatusMessage(sprintf(
                _('File %s was used'),
                $_FILES['upload']['name'],
            ), 'success');
        }

        if (\strlen($sourceurl)) {
            $this->doCurlRequest($sourceurl, 'get');

            if ($this->lastResponseCode === 200) {
                $body = $this->lastCurlResponse;
                $this->addStatusMessage(
                    sprintf(_('URL %s was used'), $sourceurl),
                    'success',
                );
            } else {
                $this->addStatusMessage(sprintf(
                    _('Error %s obataing %s'),
                    $this->lastResponseCode,
                    $sourceurl,
                ), 'success');
            }
        }

        if (null === $method) {
            $method = 'GET';
        }

        if (!\strlen($url)) {
            $url = $this->url;
            $body = null;
        } else {
            if (null !== $body) {
                $this->setPostFields($body);
            }
        }

        if (null === $format) {
            if (strstr($url, '.xml')) {
                $format = 'xml';
            } else {
                $format = 'json';
            }
        }

        if (\strlen($action)) {
            $this->setMyKey($id);
            $result = $this->performAction($action, 'int');
        } else {
            $result = $this->doCurlRequest($url, $method, $format);
        }

        return $result;
    }

    /**
     * Obtain structure for current (or given) evidence.
     *
     * @param string $evidence
     *
     * @return array Evidence structure
     */
    public function getColumnsInfo($evidence = null)
    {
        $columnsInfoFinal = [];
        $columnsInfo = parent::getColumnsInfo($evidence);

        if (\is_array($columnsInfo) && \array_key_exists('id', $columnsInfo)) {
            $idBackup = $columnsInfo['id'];
            unset($columnsInfo['id']);

            $columnsInfoFinal['id'] = $idBackup;
            $columnsInfoFinal['external-ids'] = ['name' => 'ExtID', 'title' => _('External ID'),
                'type' => 'string', 'isSortable' => 'false'];

            $columnsInfoFinal = array_merge($columnsInfoFinal, $columnsInfo);
        }

        return $columnsInfoFinal;
    }

    /**
     * Vykonej HTTP požadavek + uloz URI do $_SESSION['lasturl'].
     *
     * @see https://www.flexibee.eu/api/dokumentace/ref/urls/ Sestavování URL
     *
     * @param string $url    URL požadavku
     * @param strinf $method HTTP Method GET|POST|PUT|OPTIONS|DELETE
     * @param string $format požadovaný formát komunikace
     *
     * @return int HTTP Response CODE
     */
    public function doCurlRequest($url = null, $method = 'GET', $format = null)
    {
        $result = parent::doCurlRequest($url, $method, $format);
        $_SESSION['lasturl'] = $this->curlInfo['url'];

        return $result;
    }

    /**
     * Process abraflexi results.
     *
     * @param array $results
     */
    public static function extractResults($results)
    {
        $results = [];

        if (isset($results['results'][0]['result'])) {
            foreach ($results['results'][0]['result'] as $result) {
                [$null, $prefix, $company, $evidence, $recordId] = explode(
                    '/',
                    $result['ref'],
                );
                $results[] = $evidence.'/'.$recordId;
            }
        }

        return $results;
    }
}
