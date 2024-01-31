<?php

namespace Flexplorer;

/**
 * Flexplorer - Delete existing company.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2017-2018 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$company = empty($oPage->getRequestValue('company')) ? $_SESSION['company'] : $oPage->getRequestValue('company');

$saver = new \AbraFlexi\Company($company);

if ($saver->deleteFromAbraFlexi()) {
    $_SESSION['company'] = '';
}

$oPage->redirect('index.php');
