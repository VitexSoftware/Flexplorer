<?php

namespace Flexplorer;

/**
 * Flexplorer - Delete existing company.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$company = $_SESSION['company'];

$companer = new \AbraFlexi\Company(['dbNazev' => $company]);
$name = $companer->getDataValue('nazev');
if ($companer->deleteFromAbraFlexi()) {
    if ($companer->createNew($name)) {
        $companer->addStatusMessage(
            sprintf(_('Company %s was recreated'), $name),
            'success'
        );
    }
}

$oPage->redirect('company.php?company=' . $company);
