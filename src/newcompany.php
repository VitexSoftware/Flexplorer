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

namespace Flexplorer;

/**
 * Flexplorer - Create new company.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$name = $oPage->getRequestValue('nazev');
$useDemo = $oPage->getRequestValue('useDemo');
$country = $oPage->getRequestValue('country');
$orgType = $oPage->getRequestValue('org-type');

$companer = new \AbraFlexi\Company();

if (empty($name)) {
    $companer->addStatusMessage(_('Please enter company name'), 'warning');
} else {
    if ($companer->recordExists(['nazev' => $name])) {
        $companer->addStatusMessage(sprintf(
            _('Company %s already exists'),
            $name,
        ), 'warning');
    } else {
        if ($companer->createNew($name)) {
            $companer->addStatusMessage(sprintf(
                _('Company %s was created'),
                $name,
            ), 'success');
            $companies = $companer->getColumnsFromAbraFlexi(['dbNazev',
                'createDt'], [], 'createDt');
            ksort($companies);

            $myCompany = end($companies);
            $_SESSION['company'] = $myCompany['dbNazev'];
            $oPage->redirect('index.php?company='.$myCompany['dbNazev']);
        } else {
            $companer->addStatusMessage(sprintf(
                _('Creating company %s failed'),
                $name,
            ), 'error');
        }
    }
}

$oPage->addItem(new ui\PageTop(_('Main Page')));

$oPage->addItem(new \Ease\TWB5\Panel(
    _('Create New Company'),
    'success',
    new ui\CompanyForm($companer),
));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
