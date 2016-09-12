<?php

namespace Flexplorer;

/**
 * Flexplorer - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();


$oPage->addItem(new ui\PageTop(_('Main Page')));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
