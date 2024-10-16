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
 * Flexplorer - Download document in requested format.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2018 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$id = $oPage->getRequestValue('id');
$evidence = $oPage->getRequestValue('evidence');
$format = $oPage->getRequestValue('format');

$document = new \AbraFlexi\RO(
    is_numeric($id) ? (int) $id : $id,
    ['evidence' => $evidence],
);

if (empty($evidence)) {
    exit(_('Wrong call'));
}

$documentBody = $document->getInFormat($format);
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename='.$document->getEvidence().'_'.$document.'.pdf');
header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: '.\strlen($documentBody));
echo $documentBody;
