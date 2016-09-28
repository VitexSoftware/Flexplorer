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

    $id = $oPage->getRequestValue('id');
    if (!is_null($id)) {
        $url .= '/'.$id;
    }

    $url.= '.json';
}
$action = $oPage->getRequestValue('action');
if (is_null($action)) {
    $method = $oPage->getRequestValue('method');
} else {
    $method = 'PUT';
}
$body = $oPage->getRequestValue('body');

$oPage->addItem(new ui\PageTop(_('Query').': '.$url));

$requestTabs = new \Ease\TWB\Tabs('Request');

$requestTabs->addTab(_('Request'),
    new \Ease\TWB\Panel(_('Custom request'), 'warning',
    new ui\SendForm($url, $method, $body)));

$requestTabs->addTab(_('Response'), new ui\RecieveResponse($url),
    $oPage->isPosted() || ($oPage->getRequestValue('show') == 'result'));

if (strstr($url, '?')) {
    $overviewUrl = $url.'&inDesktopApp=true';
} else {
    $overviewUrl = $url.'?inDesktopApp=true';
}
$tabs->addTab(_('FlexiBee'),
    new \Ease\Html\IframeTag(str_replace('.json', '.html', $overviewUrl),
    ['style' => 'width: 100%; height: 600px', 'frameborder' => 0]));

$oPage->container->addItem($requestTabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
