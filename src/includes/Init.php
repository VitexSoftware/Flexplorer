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

//Initialise Gettext
$langs  = [
    'en_US' => ['en', 'English (International)'],
    'cs_CZ' => ['cs', 'Česky (Čeština)'],
];
$locale = 'en_US';
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $locale = \locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
}
if (isset($_GET['locale'])) {
    $locale = preg_replace('/[^a-zA-Z_]/', '', substr($_GET['locale'], 0, 10));
}
foreach ($langs as $code => $lang) {
    if ($locale == $lang[0]) {
        $locale = $code;
    }
}
setlocale(LC_ALL, $locale);
bind_textdomain_codeset('Flexplorer', 'UTF-8');
putenv("LC_ALL=$locale");
if (file_exists('../i18n')) {
    bindtextdomain('Flexplorer', '../i18n');
}
textdomain('Flexplorer');

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






/*
 * Objekt uživatele VSUser nebo VSAnonym
 * @global EaseUser
 */
$oUser                 = \Ease\Shared::user();
$oUser->settingsColumn = 'settings';

if (!\Ease\Shared::isCli()) {
    /* @var $oPage \Sys\WebPage */
    $oPage = new ui\WebPage();
}
