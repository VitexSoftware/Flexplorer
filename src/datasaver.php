<?php

namespace Flexplorer;

/**
 * Flexplorer - Data source.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

if (!$oUser->GetUserID()) {
    die(_('nejprve se prosím přihlaš'));
}

$saverClass = str_replace('-', '\\', $oPage->GetRequestValue('SaverClass'));
if ($saverClass == 'undefined') {
    exit;
}

$field    = $oPage->getRequestValue('Field');
$value    = $oPage->getRequestValue('Value');
$key      = $oPage->getRequestValue('Key', 'int');
$evidence = $oPage->getRequestValue('Evidence', 'string');
/**
 * @var Flexplorer Třída pro ukládající data
 */
$saver    = new $saverClass($evidence);
$saver->setMyKey($key);



if (is_null($saverClass) || is_null($field) || is_null($value) || is_null($key)) {
    header('HTTP/1.1 400 Bad Request', 400);
    die(_('Chybné volání'));
}
if (strtolower($value) == 'null') {
    $value = null;
}

$saver->takeData([$field => $value]);

$saver->insertToFlexiBee();

if ($saver->lastResponseCode != 201) {
    header('HTTP/1.1 501 Not Implemented', 501);
    $oUser->addStatusMessage(_('Error saving to FlexiBee'), 'error');
} else {
    header("HTTP/1.1 200 OK");
}

