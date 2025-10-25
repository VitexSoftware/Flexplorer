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
 * Flexplorer - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$embed = $oPage->getRequestValue('embed');
$id = $oPage->getRequestValue('id');
$evidence = $oPage->getRequestValue('evidence');
$report = $oPage->getRequestValue('report-name');

if (empty($evidence)) {
    exit(_('Wrong call'));
}

// Build PDF URL according to AbraFlexi API convention
$baseUrl = \Ease\Shared::cfg('ABRAFLEXI_URL');
$company = \Ease\Shared::cfg('ABRAFLEXI_COMPANY');

if (empty($id)) {
    // List report: /c/{company}/{evidence}.pdf
    $pdfUrl = "$baseUrl/c/$company/$evidence.pdf";
} else {
    // Single record report: /c/{company}/{evidence}/{id}.pdf
    $pdfUrl = "$baseUrl/c/$company/$evidence/$id.pdf";
}

// Add report-name parameter if specified
if (!empty($report)) {
    $pdfUrl .= '?report-name=' . urlencode(urldecode($report));
}

// Fetch PDF using cURL with authentication
$curl = curl_init($pdfUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, 
    \Ease\Shared::cfg('ABRAFLEXI_LOGIN') . ':' . \Ease\Shared::cfg('ABRAFLEXI_PASSWORD')
);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_MAXREDIRS, 5);

$documentBody = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
$curlError = curl_error($curl);
curl_close($curl);

if ($httpCode !== 200 || empty($documentBody)) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<h3>Error fetching PDF</h3>';
    echo '<p><strong>URL:</strong> ' . htmlspecialchars($pdfUrl) . '</p>';
    echo '<p><strong>HTTP Code:</strong> ' . $httpCode . '</p>';
    echo '<p><strong>Content-Type:</strong> ' . htmlspecialchars($contentType) . '</p>';
    if ($curlError) {
        echo '<p><strong>cURL Error:</strong> ' . htmlspecialchars($curlError) . '</p>';
    }
    if (!empty($documentBody) && strlen($documentBody) < 1000) {
        echo '<p><strong>Response:</strong></p><pre>' . htmlspecialchars($documentBody) . '</pre>';
    }
    exit;
}

if ($embed !== 'true') {
    // Generate filename for download
    $filename = $evidence . (empty($id) ? '' : '_' . $id) . '.pdf';
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: binary');
} else {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline');
}

// Allow iframe embedding
header('X-Frame-Options: SAMEORIGIN');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: '.\strlen($documentBody));
echo $documentBody;
