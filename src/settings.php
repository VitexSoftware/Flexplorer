<?php

namespace Flexplorer;

/**
 * Flexplorer - User Settings.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

if ($oPage->getRequestValue('refresh') == 'evidencies') {
    $evidencer = new Columner();
    $evidencer->refreshStructure();
}


$oPage->addItem(new ui\PageTop(_('Settings')));

$oPage->container->addItem(new \Ease\TWB\LinkButton('?refresh=evidencies',
    _('Refresh Evidencies structure'), 'success'));



$oPage->addItem(new ui\PageBottom());

$oPage->draw();
