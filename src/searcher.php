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

// Set JSON header early to prevent HTML output
header('Content-Type: application/json');

// Check if user is logged in without redirect
if (!\Ease\Shared::user()->getUserID()) {
    echo json_encode([]);

    exit;
}

$evidence = $oPage->getRequestValue('evidence');
$query = $oPage->getRequestValue('q');

if ($query !== null && $query !== '') {
    $_SESSION['searchQuery'] = $query;

    $found = [];

    try {
        $searcher = new Searcher($evidence);

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
    } catch (\Exception $e) {
        // Log error but return empty results for AJAX
        error_log('Searcher error: '.$e->getMessage());
    }

    echo json_encode($found);
} else {
    echo json_encode([]);
}
