<?php

/**
 * Flexplorer - An evidence page.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2020 Vitex Software
 */

namespace Flexplorer;

use ui\DataTable;
use Flexplorer\ui\PageBottom;
use Flexplorer\ui\PageTop;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$column = $oPage->getRequestValue('column');
$label = $oPage->getRequestValue('label');
$url = constant('ABRAFLEXI_URL') . '/c/' . constant('ABRAFLEXI_COMPANY');
if ($evidence) {
    $url .= '/' . $evidence;
}

if (is_null($evidence)) {
    $oPage->redirect('evidences.php');
}

if (!isset(\AbraFlexi\EvidenceList::$name[$evidence])) {
    $oPage->addStatusMessage(
        sprintf(_('Evidence %s does not exist'), $evidence),
        'warning'
    );
    $oPage->redirect('evidences.php');
} else {
    $oPage->addEvidenceToHistory($evidence);

    $oPage->addItem(new PageTop(sprintf(
        _('Evidence %s'),
        \AbraFlexi\EvidenceList::$name[$evidence]
    )));

    $evobj = new Flexplorer($evidence);

    if (
        array_key_exists(
            'evidence.php?evidence=' . $evidence,
            $_SESSION['evidence-menu'][constant('ABRAFLEXI_COMPANY')]
        )
    ) {
        $evidenceLicensed = true;
    } else {
        $evidenceLicensed = false;
    }

    $tabler = new \Flexplorer\ui\DataTable($evobj);

    $oPage->container->addItem($tabler);

    $oPage->addItem(new PageBottom());

    $oPage->draw();
}
