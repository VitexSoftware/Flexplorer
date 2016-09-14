<?php

namespace Flexplorer;

/**
 * Flexplorer - An evidence page.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$column   = $oPage->getRequestValue('column');
$action   = $oPage->getRequestValue('action');
$url      = constant('FLEXIBEE_URL').'/c/'.constant('FLEXIBEE_COMPANY');
if ($evidence) {
    $url.='/'.$evidence;
}

if (is_null($evidence)) {
    $oPage->redirect('index.php');
}

if (!is_null($action)) {
    $id = $oPage->getRequestValue('id');
    $oPage->redirect('query.php?evidence='.$evidence.'&action='.$action.'&id='.$id);
}

$oPage->addItem(new ui\PageTop(_('Evidence properties overview')));

//$oPage->container->addItem(new );

$evobj = new Flexplorer($evidence);

$tabs = new \Ease\TWB\Tabs('EviTabs');
$tabs->addTab(_('Listing'),
    new ui\DataGrid(_('Evidence'), new DataSource($evobj)));
$tabs->addTab(_('Structure'), new ui\EvidenceProperties($evobj, $column),
    isset($column));

$method = $oPage->getRequestValue('method');
$body   = $oPage->getRequestValue('body');
if (is_null($body)) {
    $body = $evobj->jsonizeData([]);
}

$tabs->addTab(_('Query'),
    new \Ease\TWB\Panel(_('User Query'), 'warning',
    new ui\SendForm($url, $method, $body)));

$overviewUrl = $evobj->getEvidenceUrl().'/properties.html?inDesktopApp=true';

$tabs->addTab(_('Items overview'),
    new \Ease\Html\IframeTag($overviewUrl,
    ['style' => 'width: 100%; height: 600px', 'frameborder' => 0]));

$oPage->container->addItem($tabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
