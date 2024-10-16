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
class Columner extends Flexplorer
{
    /**
     * Evidence used.
     */
    public ?string $evidence = 'evidence-column';

    /**
     * Path to serialized Evidencies structure.
     */
    private string $structFile;

    /**
     * Evidence handling class.
     *
     * @param string $evidence
     */
    public function __construct($evidence = null)
    {
        parent::__construct($evidence);
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

        foreach (\AbraFlexi\EvidenceList::$name as $evidencePath => $evidenceName) {
            $evidenceProperties = $this->getColumnsInfo($evidencePath);

            if (\count($evidenceProperties)) {
                $columnNames = array_keys($evidenceProperties);

                foreach ($columnNames as $columnId => $columnName) {
                    if (!\array_key_exists('title', $evidenceProperties[$columnName])) {
                        $evidenceProperties[$columnName]['title'] = '';
                    }

                    if (
                        $this->contains($what, $columnName) || $this->contains(
                            $what,
                            $evidenceProperties[$columnName]['name'],
                        ) || $this->contains(
                            $what,
                            $evidenceProperties[$columnName]['title'],
                        )
                    ) { // Column names
                        $results[] = ['id' => $columnId, 'name' => $columnName.' @ '.$evidencePath,
                            'what' => $evidenceProperties[$columnName]['name'].' / '.$evidenceName,
                            'url' => 'evidence.php?evidence='.$evidencePath.'&column='.$columnName];
                    }
                }
            }
        }

        return $results;
    }
}
