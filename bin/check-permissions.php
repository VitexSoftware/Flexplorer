<?php

/**
 * Check FlexiBee permissions for several Roles
 * 
 * Thanks to Ing. Karel Běl for idea
 */
require_once '../vendor/autoload.php';
require_once '../src/includes/config.php';

define('EASE_APPNAME', 'Flexplorer');
define('EASE_LOGGER', 'syslog|console');
define('FLEXIBEE_URL', constant('DEFAULT_FLEXIBEE_URL'));
define('FLEXIBEE_LOGIN', constant('DEFAULT_FLEXIBEE_LOGIN'));
define('FLEXIBEE_PASSWORD', constant('DEFAULT_FLEXIBEE_PASSWORD'));
define('FLEXIBEE_COMPANY', constant('DEFAULT_FLEXIBEE_COMPANY'));

$rolesToCheck = ['admin', 'jenCist', 'obchodnik', 'skladnik', 'skladSPok', 'superUziv', 'ucetni', 'mzdovyUcet', 'uzivatel'];

$results = [];

$userer = new AbraFlexi\FlexiBeeRW(null, ['evidence' => 'uzivatel']);
$userer->logBanner();
foreach ($rolesToCheck as $roleToCheck) {
    if (!$userer->recordExists(AbraFlexi\FlexiBeeRO::code($roleToCheck))) {
        $userer->insertToFlexiBee([
            'id' => AbraFlexi\FlexiBeeRO::code($roleToCheck),
            'kod' => $roleToCheck,
            'jmeno' => $roleToCheck,
            'prijmeni' => $roleToCheck,
            'password' => $roleToCheck,
            'passwordAgain' => $roleToCheck,
            'role' => AbraFlexi\FlexiBeeRO::code($roleToCheck)
        ]);
        if ($userer->lastResponseCode == 201) {
            $userer->addStatusMessage(sprintf('User %s created', $roleToCheck), 'success');
        } else {
            $userer->addStatusMessage(sprintf('User %s not created', $roleToCheck), 'error');
        }

        $checker = new \AbraFlexi\FlexiBeeRO(null, ['user' => $roleToCheck, 'password' => $roleToCheck]);
        foreach (\AbraFlexi\EvidenceList::$name as $evidenceCode => $evidenceName) {
            $checker->setEvidence($evidenceCode);
            $checker->getColumnsFromFlexibee(['id'], ['limit' => 1]);
            $results[$evidenceCode][$roleToCheck] = $checker->lastResponseCode == 200;
        }
    }
}


print_r($results);
