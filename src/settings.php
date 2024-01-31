<?php

namespace Flexplorer;

/**
 * Flexplorer - User Settings.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

require_once 'includes/Init.php';

$oPage->onlyForLogged();

if ($oPage->getRequestValue('reset') == 'history') {
    $oPage->addStatusMessage(_('History empty'));
    $_SESSION['history'] = [];
}

$oPage->addItem(new ui\PageTop(_('Settings')));

$oPage->container->addItem(new \Ease\TWB5\LinkButton(
    '?reset=history',
    _('Reset history'),
    'success'
));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
