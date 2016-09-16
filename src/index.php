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

$oPage->addItem(new ui\PageTop(_('FlexiBee info')));

$infoPanel = new \Ease\TWB\Panel(_('FlexiBee info'), 'info',
    new ui\LicenseInfo($_SESSION['license']));

$oPage->container->addItem($infoPanel);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
