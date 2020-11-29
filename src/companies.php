<?php

namespace Flexplorer;

/**
 * Flexplorer - Backups Listing.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';


$oPage->onlyForLogged();

$oPage->addItem(new ui\PageTop(_('Companies')));

$cListing = $oPage->container->addItem(new ui\CompaniesListing());

$oPage->setRequestURL($cListing->companer->curlInfo['url']);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();

