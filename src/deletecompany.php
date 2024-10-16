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
