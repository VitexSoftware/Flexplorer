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

$I = new AcceptanceTester($scenario);
$I->wantTo('perform actions and see result');
$I->amOnPage('/Flexplorer/src/login.php');
$I->seeElement('form input[name="server"]');
$I->seeElement('form input[name="login"]');
$I->seeElement('form input[name="password"]');
$I->seeElement('form button[class="btn btn-success"]');
$I->cantSee('Error');
$I->cantSee('Warning');
