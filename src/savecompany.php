<?php

namespace Flexplorer;

/**
 * Flexplorer - Create new company.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';


$oPage->onlyForLogged();

$company = $_SESSION['company'];

$saver = new Company(['dbNazev' => $company]);
$saver->saveBackupTo('/var/lib/flexplorer/backups');


$oPage->redirect('index.php');

