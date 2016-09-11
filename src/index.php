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

$requestTabs->addTab(_('FlexiBee info'),
    new ui\LicenseInfo($_SESSION['license']));


$requestTabs->addTab(_('Response'), new ui\RecieveResponse());

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
$method = $oPage->getRequestValue('method');
$body   = $oPage->getRequestValue('body');


$requestTabs->addTab(_('Request'),
    new \Ease\TWB\Panel(_('Custom request'), 'warning',
    new ui\SendForm($url, $method, $body)));


$oPage->container->addItem($requestTabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
