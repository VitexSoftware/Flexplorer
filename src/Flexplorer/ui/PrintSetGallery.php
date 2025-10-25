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

namespace Flexplorer\ui;

/**
 * Description of PrintSetGallery.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class PrintSetGallery extends \Ease\TWB5\Tabs
{
    /**
     * @param \Flexplorer\Flexplorer $engine
     * @param string $mode Either 'single' for single record or 'list' for multiple records
     */
    public function __construct($engine, $mode = 'single')
    {
        parent::__construct([], ['id' => 'PrintSet']);
        
        $recordId = $engine->getMyKey();
        $printSets = $engine->getReportsInfo();
        
        if (empty($printSets)) {
            $this->addTab(_('none'), new \Ease\Html\DivTag(
                new \Ease\TWB5\Alert('warning', _('No print reports available for this evidence.'))
            ));
            return;
        }
        
        // Filter reports based on mode
        $filteredReports = [];
        foreach ($printSets as $reportId => $printSet) {
            // predvybranyPocet can be '1' (single record) or 'N' (list/multiple)
            $reportType = $printSet['predvybranyPocet'] ?? '1';
            
            if ($mode === 'list' && $reportType === 'N') {
                // List reports don't need specific record ID
                $filteredReports[$reportId] = $printSet;
            } elseif ($mode === 'single' && $reportType === '1' && !empty($recordId)) {
                // Single record reports need record ID
                $filteredReports[$reportId] = $printSet;
            }
        }
        
        if (empty($filteredReports)) {
            $message = $mode === 'list' 
                ? _('No list-based print reports available for this evidence.')
                : _('No single-record print reports available. Please open a specific record in the editor.');
            
            $this->addTab(
                _('Info'),
                new \Ease\Html\DivTag(
                    new \Ease\TWB5\Alert('info', $message)
                )
            );
            return;
        }

        foreach ($filteredReports as $reportId => $printSet) {
            $url = 'document.php?evidence='.$engine->getEvidence().'&report-name='.$reportId.'&embed=true';
            
            if ($mode === 'single' && !empty($recordId)) {
                $url .= '&id='.$recordId;
            }
            
            $this->addAjaxTab(
                $printSet['reportName'],
                $url,
                $printSet['isDefault'] === 'true',
            );
        }
    }
}
