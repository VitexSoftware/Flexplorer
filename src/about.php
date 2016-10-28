<?php

namespace Flexplorer;

/**
 * Flexplorer - About application.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
use League\CommonMark\CommonMarkConverter;

require_once 'includes/Init.php';

$oPage->addItem(new ui\PageTop(_('About application')));

$oPage->container->addItem(_('Used Libraries').':');
$oPage->container->addItem('<br> FlexiPeeHP v'.\FlexiPeeHP\FlexiBeeRO::$libVersion.' (FlexiBee '.\FlexiPeeHP\EvidenceList::$version.')');
$oPage->container->addItem('<br> EasePHP Framework v'.\Ease\Atom::$frameworkVersion);

$oPage->container->addItem('<br/><br/><br/><br/>');

$converter = new CommonMarkConverter();

$oPage->container->addItem(new \Ease\Html\Div($converter->convertToHtml(file_get_contents('../README.md')),
    ['class' => 'jumbotron']));
$oPage->container->addItem('<br/><br/><br/><br/>');


$oPage->addItem(new ui\PageBottom());

$oPage->draw();
