<?php

namespace Flexplorer;

/**
 * Flexplorer - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$id = $oPage->getRequestValue('id');
$url = urldecode($oPage->getRequestValue('url'));
$body = $oPage->getRequestValue('body');
$action = $oPage->getRequestValue('action');
$method = $oPage->getRequestValue('method');
$format = $oPage->getRequestValue('format');
$evidence = $oPage->getRequestValue('evidence');
if (!strlen($url)) {
    $url = constant('ABRAFLEXI_URL') . '/c/';
    if (defined('ABRAFLEXI_COMPANY')) {
        $url .= constant('ABRAFLEXI_COMPANY');
    }

    if ($evidence) {
        $url .= '/' . $evidence;
    }

    if (!is_null($id)) {
        if (strstr($id, ',')) {
            $ids = [];
            foreach (explode(',', $id) as $oneID) {
                $ids [] = 'id=' . $oneID;
            }
            $url .= '/' . '(' . implode('%20or%20', $ids) . ')';
        } else {
            $url .= '/' . $id;
        }
    }

    if (is_null($format)) {
        $format = 'json';
    }

    $url .= '.' . $format;
    $url .= '?detail=full';
    $_REQUEST['url'] = $url;
}

$sender = new Flexplorer($evidence);

if ($oPage->isPosted() || strlen($url)) {
    $sender->performQuery();
}

if (strlen($action)) {
    $method = 'POST';
    $body = $sender->postFields;
}


$oPage->addItem(new ui\PageTop(_('Query') . ': ' . $url));

$requestTabs = new \Ease\TWB5\Tabs('Request');

$requestTabs->addTab(
    _('Request'),
    new \Ease\TWB5\Panel(
        _('Custom request'),
        'warning',
        new ui\SendForm($url, $method, $body, $format)
    )
);

$requestTabs->addTab(
    _('Response'),
    new ui\ShowResponse($sender),
    $oPage->isPosted() || ($oPage->getRequestValue('show') == 'result')
);

if (strstr($url, '?')) {
    $overviewUrl = $url . '&inDesktopApp=true';
} else {
    $overviewUrl = $url . '?inDesktopApp=true';
}
$requestTabs->addTab(
    _('AbraFlexi'),
    new \Ease\Html\IframeTag(
        str_replace(
            ['.json', '.xml', '.csv'],
            '.html',
            $overviewUrl
        ),
        ['style' => 'width: 100%; height: 600px', 'frameborder' => 0]
    )
);

$oPage->container->addItem($requestTabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
