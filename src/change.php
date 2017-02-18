<?php

namespace Flexplorer;

/**
 * Flexplorer - Show WebHook data recieved.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016-2017 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$file   = $oPage->getRequestValue('file');
$sender                   = new Flexplorer();
$sender->lastResponseCode = 200;
$sender->lastCurlResponse = json_encode(json_decode(file_get_contents(sys_get_temp_dir().'/'.basename($file))),
    JSON_PRETTY_PRINT);


$oPage->addItem(new ui\PageTop(_('Changes recieved').': '.$file));


$oPage->container->addItem(new ui\ShowResponse($sender));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
