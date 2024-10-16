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
     */
    public function __construct($engine)
    {
        $printSets = $engine->getReportsInfo();
        parent::__construct([], ['id' => 'PrintSet']);

        if (\count($printSets)) {
            foreach ($printSets as $reportId => $printSet) {
                $this->addAjaxTab(
                    $printSet['reportName'],
                    'document.php?evidence='.$engine->getEvidence().'&id='.$engine->getMyKey().'&report-name='.$reportId.'&embed=true',
                    $printSet['isDefault'] === 'true',
                );
            }
        } else {
            $this->addTab(_('none'), '');
        }
    }
}
