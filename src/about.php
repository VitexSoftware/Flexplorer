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

namespace Flexplorer;

/**
 * Flexplorer - About application.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2024 Vitex Software
 */

use League\CommonMark\CommonMarkConverter;

require_once 'includes/Init.php';

$oPage->addItem(new ui\PageTop(_('About application')));

$oPage->container->addItem(_('Used Libraries').':');
$oPage->container->addItem('<br> AbraFlexi v'.\Ease\Shared::appVersion('spojenet/flexibee').' (AbraFlexi '.\AbraFlexi\EvidenceList::$version.')');
$oPage->container->addItem('<br> EasePHP Framework v'.\Ease\Shared::depVersion('vitexsoftware/ease-core'));

$oPage->container->addItem('<br/><br/><br/><br/>');

$converter = new CommonMarkConverter();

$oPage->container->addItem(new \Ease\Html\DivTag(
    $converter->convertToHtml(file_get_contents('../README.md')),
    ['class' => 'jumbotron'],
));
$oPage->container->addItem('<br/><br/><br/><br/>');

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
