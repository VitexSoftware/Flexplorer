<?php

namespace Flexplorer;

/**
 * Flexplorer - Search class.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */
class Searcher extends \Ease\Atom {

    /**
     * Prohledávaná tabulka.
     *
     * @var string
     */
    public $evidence = null;

    /**
     * Prohledávaný sloupeček.
     *
     * @var string
     */
    public $column = null;

    /**
     * Pole prohledávacích obejktů.
     *
     * @var array
     */
    public $sysClasses = [];

    /**
     * Třída pro hromadné operace s konfigurací.
     *
     * @param string $evidence Třída použitá k hledání
     */
    public function __construct($evidence = null) {
        if (is_null($evidence)) {
            $this->sysClasses['evidencies'] = new Evidencer();
            $this->sysClasses['column'] = new Columner();
//            $lister    = new \AbraFlexi\EvidenceList();
//            $flexidata = $lister->getFlexiData();
//            foreach ($flexidata as $evidence) {
//                $this->registerEvidence($evidence['evidencePath']);
//            }
        } else {
            $this->registerEvidence($evidence);
        }
    }

    /**
     * Zaregistruje prohledávanou tabulku.
     *
     * @param string $evidence
     */
    public function registerEvidence($evidence) {
        $this->sysClasses[$evidence] = new Flexplorer($evidence);
    }

    /**
     * Prohledá zaregistrované tabulky.
     *
     * @param string $term
     *
     * @return array
     */
    public function searchAll($term) {
        $results = [];
        foreach ($this->sysClasses as $searched) {
            if (!is_null($this->evidence) && ($searched->getEvidence() != $this->evidence)) {
                continue;
            }
            if (!is_null($this->column)) {
                if (isset($searched->useKeywords[$this->column])) {
                    $searched->useKeywords = [$this->column => $searched->useKeywords[$this->column]];
                }
            }
            $found = $searched->searchString($term);

            if (count($found)) {
                foreach ($found as $lineNo => $values) {
                    if (isset($values['what'])) {
                        $found[$lineNo]['what'] = $values['what'];
                    } else {
                        $found[$lineNo]['what'] = current(array_keys(array_filter($values,
                                                function($var) use ($term) {
                                            return preg_match("/\b$term\b/i", $var);
                                        })));

                        if ($found[$lineNo]['what'] === false) {
                            foreach ($values as $column => $value) {
                                if (stristr($value, $term)) {
                                    $found[$lineNo]['what'] = $column;
                                    break;
                                }
                            }
                        }
                    }
                }
                $results[$searched->evidence] = $found;
            }
        }
        if (!count($results)) {
            $this->addStatusMessage(_('No search results'));
        }
        return $results;
    }

}
