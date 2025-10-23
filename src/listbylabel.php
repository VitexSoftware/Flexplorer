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

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$label = $oPage->getRequestValue('label');

$oPage->addItem(new ui\PageTop(sprintf(_('Label %s occurencies'), $label)));

if (isset($label)) {
    if (isset($evidence)) {
        $evobj = new SearchFlexplorer(['evidence' => $evidence, 'stitky' => $label]);
        $oPage->addItem(new \Ease\TWB5\LinkButton(
            'listbylabel.php?label='.$label,
            _('All Evidencies'),
            'success',
        ));
    } else {
        $evobj = new SearchFlexplorer(['stitky' => $label]);
    }
} else {
    if (isset($evidence)) {
        $evobj = new \Flexplorer\Flexplorer($evidence);
    } else {
        $evobj = new SearchFlexplorer();
    }
}

foreach ($evobj->getData() as $evidence => $occurencies) {
    if (\count($occurencies)) {
        $oPage->addItem(new \Ease\Html\H2Tag(new \Ease\Html\ATag(
            'evidence.php?evidence='.$evidence,
            $evidence,
        )));
        $results = new \Ease\Html\TableTag(
            null,
            ['class' => 'table'],
        );
        $evobj->evidenceStructure = $evobj->getColumnsInfo();

        foreach ($evobj->htmlizeData($occurencies) as $data) {
            $results->addRowColumns($data);
        }

        $oPage->addItem($results);
    }
}

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
