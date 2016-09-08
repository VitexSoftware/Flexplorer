<?php

namespace Flexplorer;

/**
 * Flexplorer - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$query    = $oPage->getRequestValue('search');




$oPage->addItem(new ui\PageTop(_('Search results')));

if (strlen($query) > 1) {
    $searcher = new Searcher($evidence);
    $results  = $searcher->searchAll($query);
    if (count($results)) {
        $resultTables = [];
        foreach ($results as $evidenceName => $evidenceResults) {
            $resultTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
            $columnNames = array_keys(current($evidenceResults));
            if (count($columnNames) > 4) {
                array_pop($columnNames);
                array_pop($columnNames);
                array_pop($columnNames);
            }
            $resultTable->addRowHeaderColumns($columnNames);
            foreach ($evidenceResults as $key => $values) {
                foreach ($values as $vkey => $vvalue) {
                    $values[$vkey] = '<a href="'.$values['url'].'">'.$vvalue.'</a>';
                }
                if (count($columnNames) > 4) {
                    unset($values['what']);
                    unset($values['url']);
                    unset($values['name']);
                }
                $resultTable->addRowColumns($values);
            }
            $resultTables[] = $resultTable;
        }
        $oPage->container->addItem(new \Ease\TWB\Panel(sprintf(_('Search for %s results in %s'),
                "<strong>$query</strong>", $evidenceName), 'info', $resultTables));
    }
}



$oPage->addItem(new ui\PageBottom());

$oPage->draw();
