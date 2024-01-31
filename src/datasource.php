<?php

namespace Flexplorer;

/**
 * Flexplorer - Data source.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
if (strlen($evidence)) {
    $datasource = new DataSource(new Flexplorer($evidence));
    $datasource->output();
} else {
    $stitek = $oPage->getRequestValue('stitek');
    if (strlen($stitek)) {
        $datasource = new DataSource(new SearchFlexplorer(['stitek' => $stitek]));

        $datasource->output();
    }
}
