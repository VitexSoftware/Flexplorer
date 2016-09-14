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

$url = $oPage->getRequestValue('url');
if (!strlen($url)) {
    $url = constant('FLEXIBEE_URL').'/c/';
    if (defined('FLEXIBEE_COMPANY')) {
        $url .= constant('FLEXIBEE_COMPANY');
    }

    $evidence = $oPage->getRequestValue('evidence');
    if ($evidence) {
        $url.='/'.$evidence;
    }
    $url.='.json';
}
$action = $oPage->getRequestValue('action');
if (is_null($action)) {
    $method = $oPage->getRequestValue('method');
} else {
    $method = 'PUT';
}
$body   = $oPage->getRequestValue('body');

$oPage->addItem(new ui\PageTop(_('Query')));

$requestTabs = new \Ease\TWB\Tabs('Request');

$requestTabs->addTab(_('Request'),
    new \Ease\TWB\Panel(_('Custom request'), 'warning',
    new ui\SendForm($url, $method, $body)));



$requestTabs->addTab(_('Response'), new ui\RecieveResponse(), $oPage->isPosted());




$oPage->container->addItem($requestTabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
