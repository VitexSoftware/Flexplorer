<?php

namespace Flexplorer;

/**
 * Flexplorer - Datový zdroj.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
if (is_null($evidence)) {
    die('?!?!?');
}
$datasource = new DataSource(new Flexplorer($evidence));
$datasource->output();
