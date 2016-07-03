<?php

namespace Flexplorer;

/**
 * Flexplorer - Datový zdroj.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$query    = $oPage->getRequestValue('q');
if (is_null($query)) {
    die('?!?!?');
}

$found = [];

$searcher = new Searcher($evidence);

header('ContentType: text/json');

if (strlen($query) > 1) {
    $results = $searcher->searchAll($query);

    foreach ($results as $rectype => $records) {
        foreach ($records as $recid => $record) {
            $found[] = ['id' => $record['id'], 'url' => 'evidence.php?evidence='.$rectype.'&amp;id='.$record['id'],
                'name' => $record[$record['what']],
                'type' => $rectype, 'what' => $record['what']];
        }
    }
}
echo json_encode($found);
