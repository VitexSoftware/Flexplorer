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

$type = $oPage->getRequestValue('type');
$operation = $oPage->getRequestValue('operation');

$scheme = $_SERVER['REQUEST_SCHEME'] ?? (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http');
$appurl = $scheme.'://'.$_SERVER['HTTP_HOST'].rtrim(\dirname(\Ease\WebPage::getUri()), '/').'/';

$xml = new xml\Winstrom();

$engine = new Flexplorer();

foreach (\AbraFlexi\EvidenceList::$name as $evidence => $evidenceName) {
    switch ($type) {
        case 'evidence':
            $xml->addItem(new \Flexplorer\xml\FelexiBeeButtonXML(
                xml\FelexiBeeButtonXML::buttonCode($evidence, 'FLEV'),
                $appurl.'evidence.php?evidence='.$evidence,
                sprintf(_('Open %s in Flexplorer'), $evidence),
                $evidenceName,
                $evidence,
                'list',
                'desktop',
            ));

            $xml->addItem(new \Flexplorer\xml\FelexiBeeButtonXML(
                xml\FelexiBeeButtonXML::buttonCode($evidence, 'FLED'),
                $appurl.'editor.php?evidence='.$evidence.'&id=${object.id}',
                _('Edit Record in Flexplorer'),
                $evidenceName,
                $evidence,
                'list',
                'desktop',
            ));

            $xml->addItem(new \Flexplorer\xml\FelexiBeeButtonXML(
                xml\FelexiBeeButtonXML::buttonCode($evidence, 'JSONROW'),
                $appurl.'query.php?format=json&show=result&evidence='.$evidence.'&id=${objectIds}',
                _('JSON in Flexplorer'),
                $evidenceName,
                $evidence,
                'list',
                'desktop',
            ));

            $xml->addItem(new \Flexplorer\xml\FelexiBeeButtonXML(
                xml\FelexiBeeButtonXML::buttonCode($evidence, 'XMLROW'),
                $appurl.'query.php?format=xml&show=result&evidence='.$evidence.'&id=${objectIds}',
                _('XML in Flexplorer'),
                $evidenceName,
                $evidence,
                'list',
                'desktop',
            ));

            break;
    }
}

if ($operation === 'install') {
    $oPage->addItem(new ui\PageTop(sprintf(
        _('flexplorer %s lexibee-buttons'),
        $type,
    )));

    $engine->setEvidence('custom-button');
    $engine->setPostFields($xml);

    $results = $engine->performRequest('', 'POST', 'xml');

    foreach ($results as $result) {
        [$evidence, $recordId] = extract('/', $result);
        $oPage->addItem(new \Ease\TWB5\LinkButton(
            'editor.php?evidence='.$evidence.'&id='.$recordId,
            $result,
        ));
    }

    $oPage->addItem(new ui\PageBottom());

    $oPage->draw();
} else {
    header('Content-Type: application/xml');
    header('Content-disposition: attachment; filename="flexplorer-'.$type.'-abraflexi-buttons.xml"');
    echo $xml;
}
