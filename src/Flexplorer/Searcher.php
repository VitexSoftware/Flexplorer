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
 * Flexplorer - Search class.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */
class Searcher extends \Ease\Atom
{
    /**
     * Prohledávaná tabulka.
     */
    public ?string $evidence = null;

    /**
     * Prohledávaný sloupeček.
     */
    public ?string $column = null;

    /**
     * Pole prohledávacích obejktů.
     */
    public array $sysClasses = [];

    /**
     * Třída pro hromadné operace s konfigurací.
     *
     * @param string $evidence Třída použitá k hledání
     */
    public function __construct($evidence = null)
    {
        $this->evidence = $evidence;
        
        if (null === $evidence || empty($evidence)) {
            // Search system evidences
            $this->sysClasses['evidencies'] = new Evidencer();
            
            // Search in all available evidences
            try {
                $lister = new \AbraFlexi\EvidenceList();
                $flexidata = $lister->getFlexiData();
                foreach ($flexidata as $evidenceInfo) {
                    if (isset($evidenceInfo['evidencePath'])) {
                        try {
                            $this->registerEvidence($evidenceInfo['evidencePath']);
                        } catch (\Exception $e) {
                            // Skip evidences that cannot be registered
                            error_log('Cannot register evidence ' . $evidenceInfo['evidencePath'] . ': ' . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                error_log('Cannot load evidence list: ' . $e->getMessage());
            }
        } else {
            $this->registerEvidence($evidence);
        }
    }

    /**
     * Zaregistruje prohledávanou tabulku.
     *
     * @param string $evidence
     */
    public function registerEvidence($evidence): void
    {
        $this->sysClasses[$evidence] = new Flexplorer($evidence);
    }

    /**
     * Prohledá zaregistrované tabulky.
     *
     * @param string $term
     *
     * @return array
     */
    public function searchAll($term)
    {
        $results = [];

        foreach ($this->sysClasses as $searched) {
            try {
                if (null !== $this->evidence && ($searched->getEvidence() !== $this->evidence)) {
                    continue;
                }

                if (null !== $this->column) {
                    if (isset($searched->useKeywords[$this->column])) {
                        $searched->useKeywords = [$this->column => $searched->useKeywords[$this->column]];
                    }
                }

                $found = $searched->searchString($term);

                if (\count($found)) {
                    foreach ($found as $lineNo => $values) {
                        if (isset($values['what'])) {
                            $found[$lineNo]['what'] = $values['what'];
                        } else {
                            $found[$lineNo]['what'] = current(array_keys(array_filter(
                                $values,
                                static function ($var) use ($term) {
                                    return preg_match("/\\b{$term}\\b/i", $var);
                                },
                            )));

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
            } catch (\Exception $e) {
                // Skip evidences that fail during search
                error_log('Search error in evidence ' . ($searched->evidence ?? 'unknown') . ': ' . $e->getMessage());
            } catch (\TypeError $e) {
                // Skip type errors from malformed data
                error_log('Type error in evidence ' . ($searched->evidence ?? 'unknown') . ': ' . $e->getMessage());
            }
        }

        if (!\count($results)) {
            $this->addStatusMessage(_('No search results'));
        }

        return $results;
    }
}
