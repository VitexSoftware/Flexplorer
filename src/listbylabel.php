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




$results = new \Ease\Html\TableTag();
foreach ($evobj->getData() as $evidence => $occurencies) {
    if (count($occurencies)) {
        $results->addItem(new \Ease\Html\ThTag($evidence));
        foreach ($occurencies as $data) {
            $results->addRowColumns($data);
        }
    }
}

$oPage->container->addItem($results);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();

