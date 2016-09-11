<?php
/**
 * Flexplorer - Search in evidence names.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

/**
 * Search in evidence names
 *
 * @author vitex
 */
class Evidencer extends Flexplorer
{
    /**
     * Evidence used.
     * @var string
     */
    public $evidence = 'evidence-list';

    /**
     * Evidencies listing
     * @var array
     */
    public $evidencies = [];

    /**
     * Evidence name searcher
     *
     * @param string $evidence
     */
    public function __construct($evidence = null)
    {
        parent::__construct($evidence);
        $this->evidencies = \FlexiPeeHP\Structure::$evidence;
    }

    /**
     * Search for match in evidences list
     *
     * @param string $what
     * @return array
     */
    public function searchString($what)
    {
        $results    = [];
        $evidenceID = 0;
        foreach ($this->evidencies as $evidencePath => $evidenceName) {
            if ($this->contains($what, $evidenceName) || $this->contains($what,
                    $evidencePath)) {
                $evidence['id']   = $evidenceID++;
                $evidence['name'] = $evidenceName;
                $evidence['what'] = $evidencePath;
                $evidence['url']  = 'evidence.php?evidence='.$evidencePath;
                $results[]        = $evidence;
            }
        }
        return $results;
    }

    /**
     * Obtain no evidence structure
     *
     * @param string $evidence
     * @return array Evidence structure
     */
    public function getColumnsInfo($evidence = null)
    {
        return [];
    }
}
