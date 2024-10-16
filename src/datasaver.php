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
 * Flexplorer - Data source.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

require_once 'includes/Init.php';

$oPage->onlyForLogged();

if (!$oUser->getUserID()) {
    exit(_('Please login First'));
}

$saverClass = str_replace('-', '\\', $oPage->GetRequestValue('SaverClass'));

if ($saverClass === 'undefined') {
    exit;
}

$field = $oPage->getRequestValue('Field');
$value = $oPage->getRequestValue('Value');
$key = $oPage->getRequestValue('Key', 'int');
$evidence = $oPage->getRequestValue('Evidence', 'string');
/**
 * @var Flexplorer Třída pro ukládající data
 */
$saver = new $saverClass($evidence);
$saver->setMyKey($key);

if (null === $saverClass || null === $field || null === $value || null === $key) {
    header('HTTP/1.1 400 Bad Request', 400);

    exit(_('Bad call'));
}

if (strtolower($value) === 'null') {
    $value = null;
}

$saver->takeData([$field => $value]);

$saver->insertToAbraFlexi();

if ($saver->lastResponseCode !== 201) {
    header('HTTP/1.1 501 Not Implemented', 501);
    $oUser->addStatusMessage(_('Error saving to AbraFlexi'), 'error');
} else {
    header('HTTP/1.1 200 OK');
}
