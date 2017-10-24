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

$name    = $oPage->getRequestValue('nazev');
$useDemo = $oPage->getRequestValue('useDemo');
$country = $oPage->getRequestValue('country');
$orgType = $oPage->getRequestValue('org-type');

$companer = new \FlexiPeeHP\Company();

if (empty($name)) {
    $companer->addStatusMessage(_('Please enter company name'), 'warning');
} else {
    if ($companer->recordExists(['nazev' => $name])) {
        $companer->addStatusMessage(sprintf(_('Company %s already exists'),
                $name), 'warning');
    } else {
        if ($companer->createNew($name)) {
            $companer->addStatusMessage(sprintf(_('Company %s was created'),
                    $name), 'success');
            $companies = $companer->getColumnsFromFlexibee(['dbNazev',
                'createDt'], [], 'createDt');
            ksort($companies);

            $myCompany           = end($companies);
            $_SESSION['company'] = $myCompany['dbNazev'];
            $oPage->redirect('index.php?company='.$myCompany['dbNazev']);
        } else {
            $companer->addStatusMessage(sprintf(_('Creating company %s failed'),
                    $name), 'error');
        }
    }
}

$oPage->addItem(new ui\PageTop(_('Main Page')));

$oPage->container->addItem(new \Ease\TWB\Panel(_('Create New Company'),
        'success', new ui\CompanyForm($companer)));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
