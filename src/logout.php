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
 * Flexplorer - Odhlašovací stránka.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2023 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';
unset($_SESSION['user'], $_SESSION['password'], $_SESSION['url'], $_SESSION['company']);

if ($oUser->getUserID()) {
    $oUser->logout();
    $messagesBackup = $oUser->getStatusMessages(true);
    \Ease\Shared::user(new \Ease\Anonym());

    foreach ($messagesBackup as $message) {
        $oPage->addStatusMessage($message);
    }

    ui\WebPage::redirect('login.php');
}

$oPage->addItem(new ui\PageTop(_('Sign out')));
$oPage->addItem('<br/><br/><br/><br/>');
$oPage->addItem(new \Ease\Html\DivTag(new \Ease\Html\ATag(
    'login.php',
    _('Thank you for your patronage and look forward to another visit'),
    ['class' => 'jumbotron'],
)));
$oPage->addItem('<br/><br/><br/><br/>');
$oPage->addItem(new ui\PageBottom());
$oPage->draw();
