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
 * Flexplorer - DataTables data source.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2024 Vitex Software
 */

require_once 'includes/Init.php';

$oPage->onlyForLogged();

header('Content-Type: application/json');

// Start output buffering to prevent unintended output
ob_start();

try {
    $class = $oPage->getRequestValue('class');
    $engine = new $class(ui\WebPage::getRequestValue('evidence'));

    unset($_REQUEST['class'], $_REQUEST['_'], $_REQUEST['XDEBUG_SESSION_START']);

    $dataRaw = $engine->getColumnsFromAbraFlexi('*', array_merge($_REQUEST, ['add-row-count' => true]));

    foreach ($dataRaw as $row => $columns) {
        $dataRaw[$row]['lastUpdate'] = (\array_key_exists('lastUpdate', $dataRaw) && $dataRaw[$row]['lastUpdate'])
            ? $dataRaw[$row]['lastUpdate']->format(\AbraFlexi\DateTime::$format)
            : '';

        foreach ($columns as $column => $value) {
            switch (\gettype($dataRaw[$row][$column])) {
                case 'array':
                    break;
                case 'object':
                    $dataRaw[$row][$column] = (string) $dataRaw[$row][$column];

                    break;

                default:
                    break;
            }
        }
    }

    $response = [
        'recordsTotal' => $engine->rowCount,
        'recordsFiltered' => $engine->rowCount,
        'data' => $dataRaw,
    ];

    // Clear any buffered output before sending JSON
    ob_clean();
    echo json_encode($response, \JSON_OBJECT_AS_ARRAY);
} catch (\Throwable $e) {
    // Handle exceptions and return a JSON error response
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // End output buffering
    ob_end_flush();
}

exit;
