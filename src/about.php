<?php

namespace Flexplorer;

/**
 * Flexplorer - Odhlašovací stránka.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

require_once 'includes/Init.php';

$oPage->addItem(new ui\PageTop(_('Odhlášení')));

$oPage->container->addItem('<br/><br/><br/><br/>');
$oPage->container->addItem(new \Ease\Html\Div(nl2br(file_get_contents('../README.md')),
    ['class' => 'jumbotron']));
$oPage->container->addItem('<br/><br/><br/><br/>');

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
