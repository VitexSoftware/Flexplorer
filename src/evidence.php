<?php

/**
 * Flexplorer - An evidence page.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2024 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$column = $oPage->getRequestValue('column');
$label = $oPage->getRequestValue('label');
$url = \Ease\Shared::cfg('ABRAFLEXI_URL') . '/c/' . \Ease\Shared::cfg('ABRAFLEXI_COMPANY');
if ($evidence) {
    $url .= '/' . $evidence;
}

if (is_null($evidence)) {
    $oPage->redirect('index.php');
}

if (!isset(\AbraFlexi\EvidenceList::$name[$evidence])) {
    $oPage->addStatusMessage(sprintf(_('Evidence %s does not exist'), $evidence), 'warning');
    $oPage->redirect('evidences.php');
} else {
    $oPage->addEvidenceToHistory($evidence);

    $oPage->addItem(new ui\PageTop(sprintf(_('Evidence %s'), \AbraFlexi\EvidenceList::$name[$evidence])));

    $evobj = new Flexplorer($evidence);

    if (array_key_exists('evidence.php?evidence=' . $evidence, $_SESSION['evidence-menu'][\Ease\Shared::cfg('ABRAFLEXI_COMPANY')])) {
        $evidenceLicensed = true;
    } else {
        $evidenceLicensed = false;
    }

    $tabs = new \Ease\TWB5\Tabs([], ['id' => 'EviTabs']);
    if ($evidenceLicensed === true) {
        $tabs->addTab(_('Listing'), new \Flexplorer\ui\DataTable($evobj));
    }
    $tabs->addTab(
        _('Column Groups'),
        new ui\ColumnsGroups($evobj, $column),
        isset($column)
    );

    $method = $oPage->getRequestValue('method');
    $body = $oPage->getRequestValue('body');
    if (is_null($body)) {
        $body = $evobj->getJsonizedData(['evidence' => $evidence]);
    }


    if ($evidenceLicensed === true) {
        $tabs->addTab(
            _('Query'),
            new \Ease\TWB5\Panel(
                _('User Query'),
                'warning',
                new ui\SendForm($url, $method, $body)
            )
        );
        $overviewUrl = $evobj->getEvidenceUrl() . '/properties.html?inDesktopApp=true';

        $tabs->addTab(
            _('Items overview'),
            new \Ease\Html\IframeTag(
                $overviewUrl,
                ['style' => 'width: 100%; height: 600px', 'frameborder' => 0]
            )
        );
        if (strstr($url, '?')) {
            $overviewUrl = $url . '&inDesktopApp=true';
        } else {
            $overviewUrl = $url . '?inDesktopApp=true';
        }

        $tabs->addTab(
            _('AbraFlexi'),
            new \Ease\Html\IframeTag(
                str_replace('.json', '.html', $overviewUrl),
                ['style' => 'width: 100%; height: 600px', 'frameborder' => 0]
            )
        );
    }

    $evidenceTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
    $evidenceTable->addRowHeaderColumns();

    $evidencer = new \AbraFlexi\EvidenceList();

    $evidenceInfo = \AbraFlexi\EvidenceList::$evidences[$evidence];

    foreach ($evidenceInfo as $porperty => $propertyData) {
        $evidenceTable->addRowColumns([$porperty, $propertyData]);
    }

    $myEvidenciesRaw = $evidencer->getColumnsFromAbraFlexi('*');
    $myEvidenceRaw = null;
    foreach ($myEvidenciesRaw as $evidenceUsedInfo) {
        if ($evidenceUsedInfo['evidencePath'] == $evidence) {
            $myEvidenceRaw = $evidenceUsedInfo;
        }
    }

    if (is_null($myEvidenceRaw)) {
        $state = new \Ease\TWB5\Badge(_('no'), 'danger');
    } else {
        $state = new \Ease\TWB5\Badge(_('yes'), 'success');
    }
    $evidenceTable->addRowColumns([_('Allowed by license'), $state]);

    $infoRow = new \Ease\TWB5\Row();
    $infoRow->addColumn(6, $evidenceTable);

    $relationsList = new \Ease\Html\UlTag();

    $evidenceNames = array_flip(\AbraFlexi\EvidenceList::$name);

    $relations = $evobj->getRelationsInfo();
    if (count($relations)) {
        $evidenciesByType = \Ease\Functions::reindexArrayBy(
            \AbraFlexi\EvidenceList::$evidences,
            'evidenceType'
        );

        foreach ($relations as $relation) {
            if (is_array($relation)) {
                if (
                        array_key_exists(
                            $relation['evidenceType'],
                            $evidenciesByType
                        )
                ) {
                    $relationsList->addItemSmart(' <a href="evidence.php?evidence=' . $evidenciesByType[$relation['evidenceType']]['evidencePath'] . '">' .
                            $relation['name'] . ' (<strong>' . $relation['evidenceType'] . '</strong> ' . $relation['url'] . ')</a>');
                } else {
                    $relationsList->addItemSmart($relation['name'] . ' (' . $relation['evidenceType'] . ' ' . $relation['url'] . ')</a>');
                }
            } else {
                $relationsList->addItemSmart($relation);
            }
        }
    }
    $relations = $infoRow->addColumn(
        6,
        new \Ease\TWB5\Panel(_('Relations'), 'info', $relationsList)
    );

    $relations->addItem(
        [
                new \Ease\TWB5\LinkButton(
                    $evobj->getEvidenceURL() . '/schema-import.xsd',
                    'XSD Import'
                ),
                new \Ease\TWB5\LinkButton(
                    $evobj->getEvidenceURL() . '/schema-export.xsd',
                    'XSD Export'
                )
            ]
    );

    $infoTab = $tabs->addTab(_('Info'), $infoRow, ($evidenceLicensed === false));

    if (array_key_exists('stitky', $evobj->getColumnsInfo())) {
        $evobj->setDataValue(
            'stitky',
            \AbraFlexi\Stitek::getAvailbleLabels($evobj)
        );
        $infoTab->addItem(new \Ease\TWB5\Panel(
            _('Labels Availble'),
            'info',
            new ui\LabelGroup($evobj)
        ));
    }

    $buttonsTab = $tabs->addTab(
        _('Custom Buttons'),
        new ui\EvidenceCustomButtons($evobj),
        ($evidenceLicensed === false)
    );

    $tabs->addTab(_('Print Sets'), new ui\PrintSetGallery($evobj));

    $oPage->container->addItem($tabs);

    $oPage->addItem(new ui\PageBottom());

    $oPage->draw();
}
