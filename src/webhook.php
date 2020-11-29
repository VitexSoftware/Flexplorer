<?php

/**
 * Flexplorer - WebHook reciever.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';



$hooker = new HookReciever();
$hooker->takeChanges($hooker->listen());
$hooker->processChanges();
