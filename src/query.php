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

$id       = $oPage->getRequestValue('id');
$url      = $oPage->getRequestValue('url');
$method   = $oPage->getRequestValue('method');
$format   = $oPage->getRequestValue('format');
$evidence = $oPage->getRequestValue('evidence');
if (!strlen($url)) {
    $url = constant('FLEXIBEE_URL').'/c/';
    if (defined('FLEXIBEE_COMPANY')) {
        $url .= constant('FLEXIBEE_COMPANY');
    }

    if ($evidence) {
        $url.='/'.$evidence;
    }

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
$body = $oPage->getRequestValue('body');

$oPage->addItem(new ui\PageTop(_('Query').': '.$url));

$requestTabs = new \Ease\TWB\Tabs('Request');

$requestTabs->addTab(_('Request'),
    new \Ease\TWB\Panel(_('Custom request'), 'warning',
    new ui\SendForm($url, $method, $body, $format)));

$requestTabs->addTab(_('Response'), new ui\ShowResponse($url),
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
