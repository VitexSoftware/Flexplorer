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
 * Description of DataTable.
 *
 * @author vitex
 */
class DataTable extends \AbraFlexi\ui\DataTables\DataTable
{
    public $js = '/javascript/jquery-datatables/dataTables.bootstrap.min.js';
    public $css = '/javascript/jquery-datatables/css/dataTables.bootstrap.min.css';

    /**
     * Prepare DataSource URI.
     *
     * @param \DBFinance\Engine $engine
     *
     * @return string Data Source URI
     */
    public function dataSourceURI($engine)
    {
        $conds = ['class' => \get_class($engine), 'evidence' => $engine->getEvidence()];

        if (null !== $engine->filter) {
            $conds = array_merge($engine->filter, $conds);
        }

        return $this->ajax2db.'?'.http_build_query($conds);
    }
}
