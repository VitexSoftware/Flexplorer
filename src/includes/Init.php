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

use Ease\Locale;
use Ease\Shared;
use Flexplorer\ui\WebPage;

require_once '../vendor/autoload.php';

require_once 'includes/config.php';
new Locale('UTF-8', '../i18n', 'flexplorer');

\define('APP_NAME', 'Flexplorer');

session_start();

if (isset($_SESSION['user'])) {
    \define('ABRAFLEXI_LOGIN', $_SESSION['user']);
}

if (isset($_SESSION['password'])) {
    \define('ABRAFLEXI_PASSWORD', $_SESSION['password']);
}

if (isset($_SESSION['url'])) {
    \define('ABRAFLEXI_URL', $_SESSION['url']);
}

if (isset($_REQUEST['company'])) {
    $_SESSION['company'] = $_REQUEST['company'];
}

if (isset($_SESSION['company'])) {
    \define('ABRAFLEXI_COMPANY', $_SESSION['company']);
}

if (isset($_SESSION['sessionid'])) {
    \define('ABRAFLEXI_AUTHSESSID', $_SESSION['sessionid']);
}

/**
 * User class object User or Anonym
 * Objekt uživatele User nebo Anonym.
 *
 * @global User|Anonym
 */
$oUser = Shared::user(null, 'Flexplorer\User');
$oUser->settingsColumn = 'settings';

if (\PHP_SAPI !== 'cli') {
    /** @var WebPage $oPage */
    $oPage = new WebPage();

    if (WebPage::isPosted()) {
        $serverURL = \Ease\Document::getRequestValue('serveruri');

        if ($serverURL) {
            \define('ABRAFLEXI_URL', $serverURL);
        }

        $sessionID = \Ease\Document::getRequestValue('sessionid');

        if ($sessionID) {
            \define('ABRAFLEXI_AUTHSESSID', $sessionID);
        }

        $company = \Ease\Document::getRequestValue('company');

        if ($sessionID) {
            \define('ABRAFLEXI_COMPANY', $sessionID);
        }
    } else {
        if (file_exists('../.env')) {
            \Ease\Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], '../.env');

            if ($oUser->isLogged() === false) {
                $oUser->tryToLogin([
                    'server' => \Ease\Shared::cfg('ABRAFLEXI_URL'),
                    'login' => \Ease\Shared::cfg('ABRAFLEXI_LOGIN'),
                    'password' => \Ease\Shared::cfg('ABRAFLEXI_PASSWORD'),
                ]);
            }
        }
    }
}
