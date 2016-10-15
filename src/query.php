<?php

namespace Flexplorer;

/**
 * Flexplorer - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$url    = $oPage->getRequestValue('url');
$format = $oPage->getRequestValue('format');
$format = $oPage->getRequestValue('format');
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
        if (strstr($id, ',')) {
            $ids = [];
            foreach (explode(',', $id) as $oneID) {
                $ids [] = 'id='.$oneID;
            }
            $url .= '/('.implode(' or ', $ids).')';
        } else {
            $url .= '/'.$id;
        }
    }

    if (is_null($format)) {
        $format = 'json';
    }

    $url.= '.'.$format;
    $url.= '?detail=full';
    $_REQUEST['url'] = $url;
}
$action = $oPage->getRequestValue('action');
$format = $oPage->getRequestValue('format');
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
    new ui\SendForm($url, $method, $body, $format)));

$requestTabs->addTab(_('Response'), new ui\RecieveResponse($url),
    $oPage->isPosted() || ($oPage->getRequestValue('show') == 'result'));

if (strstr($url, '?')) {
    $overviewUrl = $url.'&inDesktopApp=true';
} else {
    $overviewUrl = $url.'?inDesktopApp=true';
}
$requestTabs->addTab(_('FlexiBee'),
    new \Ease\Html\IframeTag(str_replace(['.json', '.xml', '.csv'], '.html',
        $overviewUrl),
    ['style' => 'width: 100%; height: 600px', 'frameborder' => 0]));

$oPage->container->addItem($requestTabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
