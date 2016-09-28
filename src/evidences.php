<?php

namespace Flexplorer;

/**
 * Flexplorer - An evidences overview page.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$oPage->addItem(new ui\PageTop(_('Evidences')));


$evidenceTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
$evidenceTable->addRowHeaderColumns(array_keys(current(\FlexiPeeHP\EvidenceList::$evidences)));

$evidencer = new \FlexiPeeHP\EvidenceList();

$myEvidenciesRaw = $evidencer->getColumnsFromFlexibee('*');

$myEvidencies = [];
foreach ($myEvidenciesRaw['evidences']['evidence'] as $myEvidence) {
    $myEvidencies[$myEvidence['evidencePath']] = $myEvidence;
}

foreach (\FlexiPeeHP\EvidenceList::$evidences as $evidence) {

    if (array_key_exists($evidence['evidencePath'],
            $myEvidencies)) {
        $properties = ['class' => 'success', 'style' => 'background: lightgray',
            'title' => _('licence ok')];
        $evidence[] = _('licence ok');
    } else {
        $properties = ['class' => 'hidden'];
        $evidence[] = _('unavialble');
    }


    $evidence['evidencePath'] = new \Ease\Html\ATag('evidence.php?evidence='.$evidence['evidencePath'],
        $evidence['evidencePath']);

    $evidenceTable->addRowColumns($evidence, $properties);
}

$oPage->container->addItem($evidenceTable);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
