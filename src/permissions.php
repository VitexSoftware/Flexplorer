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
 * Flexplorer - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->addItem(new ui\PageTop(_('Permissions overview')));

$oPage->addItem(new ui\PermissionsViewer('download/permissions.csv'));

$downloadRow = new \Ease\TWB5\Row();
$downloadRow->addColumn(4, new \Ease\TWB5\LinkButton('download/permissions.csv', '🔗 '._('Get CSV'), 'info'));
$downloadRow->addColumn(4, new \Ease\TWB5\LinkButton('download/permissions.xlsx', '🔗 '._('Get XLSx'), 'info'));
$downloadRow->addColumn(4, new \Ease\TWB5\LinkButton('download/permissions.html', '🔗 '._('Get html'), 'info'));

$oPage->addItem($downloadRow);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
