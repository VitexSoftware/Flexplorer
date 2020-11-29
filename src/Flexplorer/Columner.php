<?php

/**
 * Flexplorer - Search in column names.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

/**
 * Search in evidence names
 *
 * @author vitex
 */
class Columner extends Flexplorer {

    /**
     * Evidence used.
     * @var string
     */
    public $evidence = 'evidence-column';

    /**
     * Path to serialized Evidencies structure
     * @var string
     */
    private $structFile;

    /**
     * Evidence handling class
     * 
     * @param string $evidence
     */
    public function __construct($evidence = null) {
        parent::__construct($evidence);
    }

    /**
     * Only way how to set unexistent evidence.
     *
     * @param string $evidence
     */
    public function setEvidence($evidence) {
        $this->evidence = $evidence;
    }

    /**
     * Search for match in evidences list
     *
     * @param string $what
     * @return array
     */
    public function searchString($what) {
        $results = [];
        foreach (\AbraFlexi\EvidenceList::$name as $evidencePath => $evidenceName) {
            $evidenceProperties = $this->getColumnsInfo($evidencePath);
            if (count($evidenceProperties)) {
                $columnNames = array_keys($evidenceProperties);
                foreach ($columnNames as $columnId => $columnName) {
                    if (!array_key_exists('title', $evidenceProperties[$columnName])) {
                        $evidenceProperties[$columnName]['title'] = '';
                    }
                    if ($this->contains($what, $columnName) || $this->contains($what,
                                    $evidenceProperties[$columnName]['name']) || $this->contains($what,
                                    $evidenceProperties[$columnName]['title'])) { //Column names
                        $results[] = ['id' => $columnId, 'name' => $columnName . ' @ ' . $evidencePath,
                            'what' => $evidenceProperties[$columnName]['name'] . ' / ' . $evidenceName,
                            'url' => 'evidence.php?evidence=' . $evidencePath . '&column=' . $columnName];
                    }
                }
            }
        }
        return $results;
    }

}
