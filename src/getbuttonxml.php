<?php

namespace Flexplorer;

require_once 'includes/Init.php';

$type = $oPage->getRequestValue('type');
$operation = $oPage->getRequestValue('operation');

$appurl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname(\Ease\WebPage::getUri()) . '/';

$xml = new xml\Winstrom();

$engine = new Flexplorer();

foreach (\AbraFlexi\EvidenceList::$name as $evidence => $evidenceName) {
    switch ($type) {
        case 'evidence':
            $xml->addItem(new \Flexplorer\xml\FelexiBeeButtonXML($engine->getKod($evidence . '-FLEV',
                                    true), $appurl . 'evidence.php?evidence=' . $evidence,
                            sprintf(_('Open %s in Flexplorer'), $evidence),
                            $evidenceName, $evidence, 'list', 'desktop'));

            $xml->addItem(new \Flexplorer\xml\FelexiBeeButtonXML($engine->getKod($evidence . '-FLED',
                                    true),
                            $appurl . 'editor.php?evidence=' . $evidence . '&id=${object.id}',
                            _('Edit Record in Flexplorer'), $evidenceName, $evidence,
                            'list', 'desktop'));

            $xml->addItem(new \Flexplorer\xml\FelexiBeeButtonXML($engine->getKod($evidence . '-JSONROW',
                                    true),
                            $appurl . 'query.php?format=json&show=result&evidence=' . $evidence . '&id=${objectIds}',
                            _('JSON in Flexplorer'), $evidenceName, $evidence, 'list',
                            'desktop'));

            $xml->addItem(new \Flexplorer\xml\FelexiBeeButtonXML($engine->getKod($evidence . '-XMLROW',
                                    true),
                            $appurl . 'query.php?format=json&show=result&evidence=' . $evidence . '&id=${objectIds}',
                            _('XML in Flexplorer'), $evidenceName, $evidence, 'list',
                            'desktop'));

            break;
    }
}

if ($operation == 'install') {
    $oPage->addItem(new ui\PageTop(sprintf(_('flexplorer %s lexibee-buttons'),
                            $type)));


    $engine->setEvidence('custom-button');
    $engine->setPostFields($xml);

    $results = $engine->performRequest('', 'POST', 'xml');
    foreach ($results as $result) {
        list($evidence, $recordId) = extract('/', $result);
        $oPage->container->addItem(new \Ease\TWB\LinkButton('editor.php?evidence=' . $evidence . '&id=' . $recordId,
                        $result));
    }

    $oPage->addItem(new ui\PageBottom());

    $oPage->draw();
} else {
    header('Content-Type: application/xml');
    header('Content-disposition: attachment; filename="flexplorer-' . $type . '-abraflexi-buttons.xml"');
    echo $xml;
}
