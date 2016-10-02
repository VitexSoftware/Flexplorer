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

if (!isset(\FlexiPeeHP\EvidenceList::$name[$evidence])) {
    $oPage->addStatusMessage(sprintf(_('Evidence %s does not exist'), $evidence),
        'warning');
    $oPage->redirect('evidences.php');
} else {


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

    $infoRow = new \Ease\TWB\Row();
    $infoRow->addColumn(6, $evidenceTable);

    $relationsList = new \Ease\Html\UlTag();

    $evidenceNames = array_flip(\FlexiPeeHP\EvidenceList::$name);

    $relations = $evobj->getRelationsInfo();
    if (count($relations)) {
        foreach ($relations as $relation) {
            if (is_array($relation)) {
                if (isset(\FlexiPeeHP\EvidenceList::$name[$relation['url']])) {
                    $relationsList->addItemSmart(' <a href="evidence.php?evidence='.$relation['url'].'">'.
                        $relation['name'].' ('.$relation['evidenceType'].' <strong>'.$relation['url'].'</strong>)</a>');
                } else {
                    if (array_key_exists($relation['name'], $evidenceNames)) {
                        $relationsList->addItemSmart(' <a href="evidence.php?evidence='.$evidenceNames[$relation['name']].'"><strong>'.
                            $relation['name'].'</strong> ('.$relation['evidenceType'].' '.$relation['url'].')</a>');
                    } else {
                        if (array_key_exists(strtolower($relation['evidenceType']),
                                \FlexiPeeHP\EvidenceList::$name)) {
                            $relationsList->addItemSmart(' <a href="evidence.php?evidence='.strtolower($relation['evidenceType']).'">'.
                                $relation['name'].' (<strong>'.$relation['evidenceType'].'</strong> '.$relation['url'].')</a>');
                        } else {
                            $relationsList->addItemSmart($relation['name'].' ('.$relation['evidenceType'].' '.$relation['url'].')</a>');
                        }
                    }
                }
            } else {
                $relationsList->addItemSmart($relation);
            }
        }
    }
    $infoRow->addColumn(6,
        new \Ease\TWB\Panel(_('Relations'), 'info', $relationsList));

    $tabs->addTab(_('Info'), $infoRow);




    $oPage->container->addItem($tabs);

    $oPage->addItem(new ui\PageBottom());

    $oPage->draw();
}
