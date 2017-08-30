<?php
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
$label    = $oPage->getRequestValue('label');

$oPage->addItem(new ui\PageTop(sprintf(_('Label %s occurencies'), $label)));

if (isset($label)) {
    if (isset($evidence)) {
        $evobj = new SearchFlexplorer(['evidence' => $evidence, 'stitky' => $label]);
        $oPage->container->addItem(new \Ease\TWB\LinkButton('listbylabel.php?label='.$label,
            _('All Evidencies'), 'success'));
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
    if (count($occurencies)) {
        $oPage->container->addItem(new \Ease\Html\H2Tag(new \Ease\Html\ATag('evidence.php?evidence='.$evidence,
            $evidence)));
        $results                  = new \Ease\Html\TableTag(null,
            ['class' => 'table']);
        $evobj->evidenceStructure = $evobj->getColumnsInfo();
        foreach ($evobj->htmlizeData($occurencies) as $data) {

            $results->addRowColumns($data);
        }
        $oPage->container->addItem($results);
    }
}


$oPage->addItem(new ui\PageBottom());

$oPage->draw();

