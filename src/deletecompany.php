<?php

namespace Flexplorer;

/**
 * Flexplorer - Delete existing company.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017-2018 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';


$oPage->onlyForLogged();

$company = empty($oPage->getRequestValue('company')) ? $_SESSION['company']  : $oPage->getRequestValue('company') ;

$saver = new \FlexiPeeHP\Company($company);

if ($saver->deleteFromFlexiBee()) {
    $_SESSION['company'] = '';
}

$oPage->redirect('index.php');

