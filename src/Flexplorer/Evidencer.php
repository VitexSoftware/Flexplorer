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
 * Search in evidence names.
 *
 * @author vitex
 */
class Evidencer extends Flexplorer
{
    /**
     * Evidence used.
     */
    public ?string $evidence = 'evidence-list';

    /**
     * Evidencies listing.
     */
    public array $evidencies = [];

    /**
     * Evidence name searcher.
     *
     * @param string $evidence
     */
    public function __construct($evidence = null)
    {
        parent::__construct($evidence);
        $this->evidencies = \AbraFlexi\EvidenceList::$name;
    }

    /**
     * Only way how to set unexistent evidence.
     *
     * @param string $evidence
     */
    public function setEvidence($evidence): void
    {
        $this->evidence = $evidence;
    }

    /**
     * Search for match in evidences list.
     *
     * @param string $what
     *
     * @return array
     */
    public function searchString($what)
    {
        $results = [];
        $evidenceID = 0;

        foreach ($this->evidencies as $evidencePath => $evidenceName) {
            if (
                $this->contains($what, $evidenceName) || $this->contains(
                    $what,
                    $evidencePath,
                )
            ) {
                $evidence['id'] = $evidenceID++;
                $evidence['name'] = $evidenceName;
                $evidence['what'] = $evidencePath;
                $evidence['url'] = 'evidence.php?evidence='.$evidencePath;
                $results[] = $evidence;
            }
        }

        return $results;
    }

    /**
     * Obtain no evidence structure.
     *
     * @param string $evidence
     *
     * @return array Evidence structure
     */
    public function getColumnsInfo($evidence = null)
    {
        return [];
    }
}
