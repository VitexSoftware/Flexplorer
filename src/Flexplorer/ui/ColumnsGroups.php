<?php

/**
 * Flexplorer - Column Groups tabs.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of ColumnsVisibility
 *
 * @author vitex
 */
class ColumnsGroups extends \Ease\TWB5\Tabs
{

    function __construct($evobj, $column)
    {
        parent::__construct([],['id'=>_('ColumnsGroups')]);
        $this->addTab(_('Mandatory'), new EvidenceProperties($evobj, $column, 'mandatory'));
        $this->addTab(_('ID List'), new EvidenceProperties($evobj, $column, 'inId'));
        $this->addTab(_('Summary List'), new EvidenceProperties($evobj, $column, 'inSummary'));
        $this->addTab(_('Full List'), new EvidenceProperties($evobj, $column, 'inDetail'));
        $this->addTab(_('Sortable'), new EvidenceProperties($evobj, $column, 'isSortable'));
        $this->addTab(_('Non Sortable'), new EvidenceProperties($evobj, $column, '!isSortable'));
        $this->addTab(_('Writable'), new EvidenceProperties($evobj, $column, 'isWritable'));
        $this->addTab(_('Read Only'), new EvidenceProperties($evobj, $column, '!isWritable'));
    }
}
