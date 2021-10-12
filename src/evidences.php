<?php

namespace Flexplorer;

/**
 * Flexplorer - An evidences overview page.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$oPage->addItem(new ui\PageTop(_('Evidences')));

$evidencer = new \AbraFlexi\EvidenceList();

$myEvidencies = \Ease\Functions::reindexArrayBy($evidencer->getAllFromAbraFlexi(),
                'evidencePath');

$headerColumns = array_keys(\AbraFlexi\EvidenceList::$evidences['adresar']);

$allEvidencesTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
$allEvidencesTable->addRowHeaderColumns(array_merge($headerColumns, [_('License')]));

$availbleEvidencesTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
$availbleEvidencesTable->addRowHeaderColumns($headerColumns);

$unlicensedEvidencesTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
$unlicensedEvidencesTable->addRowHeaderColumns($headerColumns);

$evidenceTabs = new \Ease\TWB\Tabs('EvidenceTabs');

$availbleCount = count($myEvidencies);
$allCount = count(\AbraFlexi\EvidenceList::$evidences);
$unlicensedCount = 0;
foreach (\AbraFlexi\EvidenceList::$evidences as $evidence) {
    if (!array_key_exists($evidence['evidencePath'], $myEvidencies)) {
        $unlicensedCount++;
    }
}

$availbleEvidencesLabel = new \Ease\TWB\Label('success', $availbleCount);

$availble = $evidenceTabs->addTab(sprintf(_('Availble %s'),
                $availbleEvidencesLabel), $availbleEvidencesTable);

$unlicensedEvidencesLabel = new \Ease\TWB\Label('warning', $unlicensedCount);
$unlicensed = $evidenceTabs->addTab(sprintf(_('Unlicensed %s'),
                $unlicensedEvidencesLabel), $unlicensedEvidencesTable);

$allEvidencesLabel = new \Ease\TWB\Label('info', $allCount);

$allEvidences = $evidenceTabs->addTab(sprintf(_('All %s'),
                $allEvidencesLabel->__toString()), $allEvidencesTable);

foreach (\AbraFlexi\EvidenceList::$evidences as $evidence) {
    $path = $evidence['evidencePath'];

    $evidence['evidencePath'] = new \Ease\Html\ATag('evidence.php?evidence=' . $evidence['evidencePath'],
            $evidence['evidencePath']);

    $evidence['importStatus'] = new \Ease\TWB\Label(str_replace(['SUPPORTED', 'NOT_DOCUMENTED',
                'DISALLOWED', 'NOT_DIRECT'],
                    ['success', 'default', 'danger', 'warning'],
                    $evidence['importStatus']), $evidence['importStatus']);

    if (array_key_exists($path, $myEvidencies)) {
        $availbleEvidencesTable->addRowColumns($evidence);
        $evidence[] = _('licence ok');
    } else {
        $unlicensedEvidencesTable->addRowColumns($evidence);
        $evidence[] = _('unavialble');
    }

    $allEvidences->addRowColumns($evidence);
}



$oPage->container->addItem($evidenceTabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
