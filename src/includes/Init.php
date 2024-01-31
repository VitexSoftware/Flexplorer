<?php

/**
 * System.Spoje.Net - Init aplikace.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2015 Spoje.Net
 */

namespace Flexplorer;

use Ease\Locale;
use Ease\Shared;
use Flexplorer\ui\WebPage;

require_once '../vendor/autoload.php';
require_once 'includes/config.php';
new Locale('UTF-8', '../i18n', 'flexplorer');
session_start();
if (isset($_SESSION['user'])) {
    define('ABRAFLEXI_LOGIN', $_SESSION['user']);
}
if (isset($_SESSION['password'])) {
    define('ABRAFLEXI_PASSWORD', $_SESSION['password']);
}
if (isset($_SESSION['url'])) {
    define('ABRAFLEXI_URL', $_SESSION['url']);
}

if (isset($_REQUEST['company'])) {
    $_SESSION['company'] = $_REQUEST['company'];
}

if (isset($_SESSION['company'])) {
    define('ABRAFLEXI_COMPANY', $_SESSION['company']);
}

if (isset($_SESSION['sessionid'])) {
    define('ABRAFLEXI_AUTHSESSID', $_SESSION['sessionid']);
}

/**
 * User class object User or Anonym
 * Objekt uživatele User nebo Anonym
 *
 * @global User|Anonym
 */
if (file_exists('../.env')) {
    \Ease\Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], '../.env');
}

$oUser = Shared::user(null, 'Flexplorer\User');
$oUser->settingsColumn = 'settings';
if (PHP_SAPI != 'cli') {
    /* @var $oPage WebPage */
    $oPage = new WebPage();
    $serverURL = \Ease\Document::getRequestValue('serveruri');
    if ($serverURL) {
        define('ABRAFLEXI_URL', $serverURL);
    }

    $sessionID = \Ease\Document::getRequestValue('sessionid');
    if ($sessionID) {
        define('ABRAFLEXI_AUTHSESSID', $sessionID);
    }

    $company = \Ease\Document::getRequestValue('company');
    if ($sessionID) {
        define('ABRAFLEXI_COMPANY', $sessionID);
    }
}
