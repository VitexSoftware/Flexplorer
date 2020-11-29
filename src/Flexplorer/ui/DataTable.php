<?php

/**
 * Flexplorer - DataTable adapter.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2020 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of DataTable
 *
 * @author vitex
 */
class DataTable extends \AbraFlexi\ui\DataTables\DataTable {

    /**
     * Prepare DataSource URI 
     * 
     * @param \DBFinance\Engine $engine
     * 
     * @return string Data Source URI
     */
    public function dataSourceURI($engine) {
        $conds = ['class' => get_class($engine), 'evidence' => $engine->getEvidence()];
        if (!is_null($engine->filter)) {
            $conds = array_merge($engine->filter, $conds);
        }
        return $this->ajax2db . '?' . http_build_query($conds);
    }

}
