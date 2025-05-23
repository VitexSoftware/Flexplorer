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

/**
 * Flexplorer - Datový zdroj.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');
$query = $oPage->getRequestValue('q');

if (\strlen($query)) {
    $_SESSION['searchQuery'] = $query;

    $found = [];

    $searcher = new Searcher($evidence);

    header('ContentType: text/json');

    if (\strlen($query) > 1) {
        $results = $searcher->searchAll($query);

        foreach ($results as $rectype => $records) {
            foreach ($records as $recid => $record) {
                if (isset($record['url'])) {
                    $url = $record['url'];
                } else {
                    $url = 'evidence.php?evidence='.$rectype.'&amp;id='.$record['id'];
                }

                if (isset($record['name'])) {
                    $name = $record['name'];
                } else {
                    $name = $record[$record['what']];
                }

                if (isset($record['what'])) {
                    $what = $record['what'];
                } else {
                    $what = $record[$record['what']];
                }

                $found[] = ['id' => $record['id'], 'url' => $url,
                    'name' => $name,
                    'type' => $rectype,
                    'what' => $what];
            }
        }
    }

    echo json_encode($found);
}
