<?php

namespace Flexplorer;

/**
 * Flexplorer - Editor záznamu.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');

$engine = new Flexplorer($evidence);
$engine->loadFromFlexiBee($oPage->getRequestValue('id', 'int'));

$oPage->addItem(new ui\PageTop(_('Editor')));

$oPage->container->addItem(new \Ease\TWB\Panel($evidence.' '.$engine, 'info',
    new ui\Editor($engine)));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
