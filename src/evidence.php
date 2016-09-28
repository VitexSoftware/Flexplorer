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

$oPage->addItem(new ui\PageTop(sprintf(_('Evidence %s'),
        \FlexiPeeHP\EvidenceList::$name[$evidence])));

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
if (strstr($url, '?')) {
    $overviewUrl = $url.'&inDesktopApp=true';
} else {
    $overviewUrl = $url.'?inDesktopApp=true';
}
$tabs->addTab(_('FlexiBee'),
    new \Ease\Html\IframeTag(str_replace('.json', '.html', $overviewUrl),
    ['style' => 'width: 100%; height: 600px', 'frameborder' => 0]));


$evidenceTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
$evidenceTable->addRowHeaderColumns();

$evidencer = new \FlexiPeeHP\EvidenceList();

$evidenceInfo = \FlexiPeeHP\EvidenceList::$evidences[$evidence];

foreach ($evidenceInfo as $porperty => $propertyData) {
    $evidenceTable->addRowColumns([$porperty, $propertyData]);
}

$myEvidenciesRaw = $evidencer->getColumnsFromFlexibee('*');
$myEvidenceRaw   = null;
foreach ($myEvidenciesRaw['evidences']['evidence'] as $evidenceUsedInfo) {
    if ($evidenceUsedInfo['evidencePath'] == $evidence) {
        $myEvidenceRaw = $evidenceUsedInfo;
    }
}

if (is_null($myEvidenceRaw)) {
    $state = _('no');
} else {
    $state = _('yes');
}
$evidenceTable->addRowColumns([_('Allowed by license'), $state]);

$tabs->addTab(_('Info'), $evidenceTable);




$oPage->container->addItem($tabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
