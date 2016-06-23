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

$oPage->addItem(new ui\PageTop(_('Flexplorer')));

$requestTabs = new \Ease\TWB\Tabs('Request');

$url      = constant('FLEXIBEE_URL').'/c/'.constant('FLEXIBEE_COMPANY');
$evidence = $oPage->getRequestValue('evidence');
if ($evidence) {
    $url.='/'.$evidence;
}
$url.='.json';


$requestTabs->addTab(_('Odpověď'), new ui\RecieveResponse());

$url    = $oPage->getRequestValue('url');
$method = $oPage->getRequestValue('method');
$body   = $oPage->getRequestValue('body');


$requestTabs->addTab(_('Požadavek'), new ui\SendForm($url, $method, $body));


$oPage->container->addItem($requestTabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
