<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of PrintSetGallery
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class PrintSetGallery extends \Ease\TWB\Tabs
{

    /**
     * 
     * @param \Flexplorer\Flexplorer $engine
     */
    public function __construct($engine)
    {
        $printSets = $engine->getFlexiData($engine->getEvidenceURL().'/reports');
        parent::__construct('PrintSet');
        if (count($printSets)) {
            foreach ($printSets['reports'] as $printSet) {
                $this->addAjaxTab(
                    htmlentities( $printSet['reportName']),
                    'document.php?evidence='.$engine->getEvidence().'&id='.$engine->getMyKey().'&report-name='.$printSet['reportId'].'&embed=true',
                    $printSet['isDefault'] == 'true'
                );
            }
        }
    }
}
