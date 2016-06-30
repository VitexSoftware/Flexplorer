<?php

namespace Flexplorer;

/**
 * Flexplorer - Nastavení uživatele stránka.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();



$oPage->addItem(new ui\PageTop(_('Nastavení')));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
