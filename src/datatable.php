<?php

declare(strict_types=1);

/**
 * This file is part of the Flexplorer package
 *
 * github.com/VitexSoftware/Flexplorer
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flexplorer;

use Flexplorer\ui\PageBottom;
use Flexplorer\ui\PageTop;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$column = $oPage->getRequestValue('column');
$label = $oPage->getRequestValue('label');
$url = \constant('ABRAFLEXI_URL').'/c/'.\constant('ABRAFLEXI_COMPANY');

if ($evidence) {
    $url .= '/'.$evidence;
}

if (null === $evidence) {
    $oPage->redirect('evidences.php');
}

if (!isset(\AbraFlexi\EvidenceList::$name[$evidence])) {
    $oPage->addStatusMessage(
        sprintf(_('Evidence %s does not exist'), $evidence),
        'warning',
    );
    $oPage->redirect('evidences.php');
} else {
    $oPage->addEvidenceToHistory($evidence);

    $oPage->addItem(new PageTop(sprintf(
        _('Evidence %s'),
        \AbraFlexi\EvidenceList::$name[$evidence],
    )));

    $evobj = new Flexplorer($evidence);

    if (
        \array_key_exists(
            'evidence.php?evidence='.$evidence,
            $_SESSION['evidence-menu'][\constant('ABRAFLEXI_COMPANY')],
        )
    ) {
        $evidenceLicensed = true;
    } else {
        $evidenceLicensed = false;
    }

    $tabler = new \Flexplorer\ui\DataTable($evobj);

    $oPage->addItem($tabler);

    $oPage->addItem(new PageBottom());

    $oPage->draw();
}
