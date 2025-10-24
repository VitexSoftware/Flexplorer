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

namespace Flexplorer\ui;

/**
 * Embed Responsive PDF viewer.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class EmbedResponsivePDF extends \Ease\Html\DivTag
{
    /**
     * @param \AbraFlexi\RO $document AbraFlexi document object
     * @param string $pdfUrl URL to PDF endpoint
     * @param string|null $reportName Report name parameter
     */
    public function __construct($document, $pdfUrl, $reportName = null)
    {
        parent::__construct(null, ['class' => 'embed-responsive embed-responsive-16by9', 'style' => 'height: 80vh;']);
        
        $params = [
            'evidence' => $document->getEvidence(),
            'embed' => 'true'
        ];
        
        $recordId = $document->getMyKey();
        if (!empty($recordId)) {
            $params['id'] = $recordId;
        }
        
        if (!empty($reportName)) {
            $params['report-name'] = $reportName;
        }
        
        $pdfSrc = $pdfUrl . '?' . http_build_query($params);
        
        $this->addItem(new \Ease\Html\IframeTag($pdfSrc, [
            'class' => 'embed-responsive-item',
            'style' => 'width: 100%; height: 100%; border: none;',
            'type' => 'application/pdf'
        ]));
    }
}
