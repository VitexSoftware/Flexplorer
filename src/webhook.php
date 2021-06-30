<?php

/**
 * Flexplorer - WebHook reciever.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2021Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$hooker = new \AbraFlexi\Bricks\HookReciever();
$hooker->takeChanges($hooker->listen());
$hooker->processChanges();
