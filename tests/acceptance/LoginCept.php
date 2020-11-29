<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('perform actions and see result');
$I->amOnPage('/Flexplorer/src/login.php');
$I->seeElement("form input[name=\"server\"]");
$I->seeElement("form input[name=\"login\"]");
$I->seeElement("form input[name=\"password\"]");
$I->seeElement("form button[class=\"btn btn-success\"]");
$I->cantSee('Error');
$I->cantSee('Warning');

