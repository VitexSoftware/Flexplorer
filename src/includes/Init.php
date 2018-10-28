<?php
/**
 * System.Spoje.Net - Init aplikace.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2015 Spoje.Net
 */

namespace Flexplorer;

require_once 'includes/config.php';
require_once '../vendor/autoload.php';

\Ease\Shared::initializeGetText('flexplorer', 'UTF-8', '../i18n');

session_start();

if (isset($_SESSION['user'])) {
    define('FLEXIBEE_LOGIN', $_SESSION['user']);
}
if (isset($_SESSION['password'])) {
    define('FLEXIBEE_PASSWORD', $_SESSION['password']);
}
if (isset($_SESSION['url'])) {
    define('FLEXIBEE_URL', $_SESSION['url']);
}

if (isset($_REQUEST['company'])) {
    $_SESSION['company'] = $_REQUEST['company'];
}

if (isset($_SESSION['company'])) {
    define('FLEXIBEE_COMPANY', $_SESSION['company']);
}

if (isset($_SESSION['sessionid'])) {
    define('FLEXIBEE_AUTHSESSID', $_SESSION['sessionid']);
}

/**
 * User class object User or Anonym
 * Objekt uživatele User nebo Anonym
 *
 * @global User|Anonym
 */
$oUser                 = \Ease\Shared::user();
$oUser->settingsColumn = 'settings';

if (!\Ease\Shared::isCli()) {
    /* @var $oPage \Sys\WebPage */
    $oPage = new ui\WebPage();

    $serverURL = $oPage->getRequestValue('serveruri');
    if ($serverURL) {
        define('FLEXIBEE_URL', $serverURL);
    }

    $sessionID = $oPage->getRequestValue('sessionid');
    if ($sessionID) {
        define('FLEXIBEE_AUTHSESSID', $sessionID);
    }
    
    $company = $oPage->getRequestValue('company');
    if ($sessionID) {
        define('FLEXIBEE_COMPANY', $sessionID);
    }

    
}
