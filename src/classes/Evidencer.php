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

        $structFile = \sys_get_temp_dir().'/flexplorer-flexibee-evidencies.struct';
        if (file_exists($structFile)) {
            $this->evidencies = unserialize(file_get_contents($structFile));
        } else {
            $this->evidencies = $this->getColumnsFromFlexibee(['evidencePath', 'evidenceName']);
            file_put_contents($structFile, serialize($this->evidencies));
        }
    }

    /**
     * Search for match in evidences list
     *
     * @param string $what
     * @return array
     */
    function searchString($what)
    {
        $results    = [];
        foreach ($this->evidencies['evidences']['evidence'] as $evidenceID => $evidence) {
            if ($this->contains($what, $evidence['evidenceName']) || $this->contains($what,
                    $evidence['evidencePath'])) {
                $evidence['id']   = $evidenceID;
                $evidence['name'] = $evidence['evidenceName'];
                $evidence['what'] = $evidence['evidencePath'];
                $evidence['url']  = 'evidence.php?evidence='.$evidence['evidencePath'];
                unset($evidence['evidenceType']);
                $results[]        = $evidence;
            }
        }
        return $results;
    }
}
