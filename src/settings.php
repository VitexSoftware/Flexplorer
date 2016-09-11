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



$oPage->addItem(new ui\PageTop(_('Settings')));

$oPage->container->addItem(_('Nothig here yet'));



$oPage->addItem(new ui\PageBottom());

$oPage->draw();
