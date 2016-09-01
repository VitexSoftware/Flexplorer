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
     * Evidence užitá objektem.
     *
     * @var string
     */
    public $evidence = 'evidence-list';

    /**
     * Search for match in evidences list
     *
     * @param string $what
     * @return array
     */
    function searchString($what)
    {
        $results    = [];
        $evidencies = $this->getColumnsFromFlexibee(['evidencePath', 'evidenceName']);
        foreach ($evidencies['evidences']['evidence'] as $evidenceID => $evidence) {
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

    /**
     * Checks to see of a string contains a particular substring
     *
     * @param $substring the substring to match
     * @param $string the string to search
     * @return true if $substring is found in $string, false otherwise
     */
    function contains($substring, $string)
    {
        $pos = strpos($string, $substring);

        if ($pos === false) {
            // string needle NOT found in haystack
            return false;
        } else {
            // string needle found in haystack
            return true;
        }
    }

}
