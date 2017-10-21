<?php

namespace Flexplorer;

require_once 'includes/Init.php';

$type      = $oPage->getRequestValue('type');
$operation = $oPage->getRequestValue('operation');

$appurl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].dirname(\Ease\WebPage::getUri()).'/';

$xml = new xml\Winstrom();

$engine = new Flexplorer();

foreach (\FlexiPeeHP\EvidenceList::$name as $evidence => $evidenceName) {
    switch ($type) {
        case 'evidence':
            $xml->addItem(new \Flexplorer\xml\FelexiBeeButtonXML($engine->getKod($evidence.'-FLEV',
                        true), $appurl.'evidence.php?evidence='.$evidence,
                    'Flexplorer', $evidenceName, $evidence, 'list', 'automatic'));
            break;
    }
}

if ($operation == 'install') {
    $oPage->addItem(new ui\PageTop(sprintf(_('flexplorer %s lexibee-buttons'),
                $type)));

    $installer = new \FlexiPeeHP\FlexiBeeRW(null,
        ['evidence' => 'custom-button']);
    $installer->setPostFields($xml);

    $results = $installer->performRequest(null, 'POST', 'xml');
    foreach (Flexplorer\extractResults($results) as $result) {
        list($evidence, $recordId) = extract('/', $result);
        $oPage->container->addItem(new \Ease\TWB\LinkButton('editor.php?evidence='.$evidence.'&id='.$recordId,
                $result));
    }

    $oPage->addItem(new ui\PageBottom());

    $oPage->draw();
} else {
    header('Content-Type: application/xml');
    header('Content-disposition: attachment; filename="flexplorer-'.$type.'-flexibee-buttons.xml"');
    echo $xml;
}
