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

if ($oPage->onlyForLogged()) {
    exit;
}

header('Content-Type: application/json; charset=utf-8');
$evidence = $_GET['evidence'] ?? '';

if (!\is_string($evidence) || !isset(\AbraFlexi\EvidenceList::$name[$evidence])) {
    http_response_code(400);
    echo json_encode(['error' => _('Unknown evidence.')]);

    exit;
}

$properties = \AbraFlexi\Functions::getOfflineColumnsInfo($evidence);

if ($properties === null) {
    http_response_code(404);
    echo json_encode(['error' => _('Properties are not available for this evidence.')]);

    exit;
}

$result = [];

foreach ($properties as $key => $property) {
    $result[] = [
        'name' => $key,
        'label' => $property['name'] ?? $key,
        'type' => $property['type'] ?? 'string',
    ];
}

echo json_encode($result, \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR);
