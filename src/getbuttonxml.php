<?php

namespace Flexplorer;

require_once 'includes/Init.php';

$type = $oPage->getRequestValue('type');

header('Content-Type: application/xml');
header('Content-disposition: attachment; filename="flexplorer-'.$type.'-custom-buttons.xml"');

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

echo $xml;
