<?php
/**
 * Flexplorer - Search in column names.
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
class Columner extends Flexplorer
{
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
    public function __construct($evidence = null)
    {
        $this->structFile = \sys_get_temp_dir().'/flexplorer-flexibee.struct';
        parent::__construct($evidence);
    }

    /**
     * Search for match in evidences list
     *
     * @param string $what
     * @return array
     */
    public function searchString($what)
    {
        $results = [];
        if (file_exists($this->structFile)) {
            $structure = $this->getStructure();
            foreach ($structure as $evidencePath => $evidenceProperties) {
                $columnNames = array_keys($evidenceProperties);
                foreach ($columnNames as $columnId => $columnName) {
                    if ($this->contains($what, $columnName) || $this->contains($what,
                            $evidenceProperties[$columnName]['name']) || $this->contains($what,
                            $evidenceProperties[$columnName]['title'])) { //Column names
                        $results[] = ['id' => $columnId, 'name' => $columnName,
                            'what' => $evidencePath,
                            'url' => 'evidence.php?evidence='.$evidencePath.'&column='.$columnName];
                    }
                }
                
            }
        } else {
            $this->addStatusMessage(_('Please refresh FlexiBee evidencies structure on Settings page'),
                'warning');
        }

        return $results;
    }

    /**
     * Obtain structure from all evidencies
     *
     * @return array
     */
    public function obtainWholeStructure()
    {
        $structure  = [];
        $this->setEvidence('evidence-list');
        $evidencies = $this->getColumnsFromFlexibee(['evidencePath', 'evidenceName']);
        foreach ($evidencies['evidences']['evidence'] as $evidenceID => $evidence) {
            $structure[$evidence['evidencePath']] = parent::getColumnsInfo($evidence['evidencePath']);
        }
        return $structure;
    }

    /**
     * Refresh structure for all evidencies
     */
    public function refreshStructure()
    {
        //DeleteAll Cached structure files

        $d     = dir(sys_get_temp_dir());
        while (false !== ($entry = $d->read())) {
            if ($this->contains('flexplorer-', $entry)) {
                unlink($d->path.'/'.$entry);
            }
        }
        $d->close();
        $this->saveStructure($this->obtainWholeStructure());
        $this->addStatusMessage(_('Structure refreshed'), 'success');
    }

    /**
     * Obtain all evidencies structure
     *
     * @return array
     */
    public function getStructure()
    {
        return unserialize(file_get_contents($this->structFile));
    }

    /**
     * Save all evidencies structure
     *
     * @param array $structure
     * @return type
     */
    public function saveStructure($structure)
    {
        return file_put_contents($this->structFile, serialize($structure));
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
