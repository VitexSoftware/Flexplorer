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

$evidence = $oPage->getRequestValue('evidence');
if (is_null($evidence)) {
    $oPage->redirect('index.php');
    exit();
}

$oPage->addItem(new ui\PageTop(_('Přehled vlastností evidence')));

//$oPage->container->addItem(new );

$evobj = new Flexplorer($evidence);

$tabs = new \Ease\TWB\Tabs('EviTabs');
$tabs->addTab(_('Výpis'), new ui\DataGrid(_('Evidence'),
 new DataSource($evobj)));
$tabs->addTab(_('Struktura'), new ui\EvidenceProperties($evobj));


$url      = constant('FLEXIBEE_URL').'/c/'.constant('FLEXIBEE_COMPANY');
$evidence = $oPage->getRequestValue('evidence');
if ($evidence) {
    $url.='/'.$evidence;
}


$tabs->addTab(_('Dotaz'), new ui\SendForm($url));


$oPage->container->addItem($tabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
