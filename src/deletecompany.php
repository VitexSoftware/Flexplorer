<?php

namespace Flexplorer;

/**
 * Flexplorer - Delete existing company.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';


$oPage->onlyForLogged();

$company = $_SESSION['company'];

$saver = new \FlexiPeeHP\Company(['dbNazev' => $company]);

if ($saver->deleteFromFlexiBee()) {
    $_SESSION['company'] = '';
}

$oPage->redirect('index.php');

