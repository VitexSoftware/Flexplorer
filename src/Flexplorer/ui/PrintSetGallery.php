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
class PrintSetGallery extends \Ease\TWB5\Tabs
{
    /**
     *
     * @param \Flexplorer\Flexplorer $engine
     */
    public function __construct($engine)
    {
        $printSets = $engine->getReportsInfo();
        parent::__construct('PrintSet');
        if (count($printSets)) {
            foreach ($printSets as $reportId => $printSet) {
                $this->addAjaxTab(
                    ($printSet['reportName']),
                    'document.php?evidence=' . $engine->getEvidence() . '&id=' . $engine->getMyKey() . '&report-name=' . $reportId . '&embed=true',
                    $printSet['isDefault'] == 'true'
                );
            }
        } else {
            $this->addTab(_('none'));
        }
    }
}
