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

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');

if (null === $evidence) {
    $oPage->redirect('index.php');

    exit;
}

$oPage->addItem(new ui\PageTop(_('Evidence proprties overview')));

// $oPage->container->addItem(new );

$evobj = new Flexplorer($evidence);

$tabs = new \Ease\TWB5\Tabs('EviTabs');
$tabs->addTab(_('Listing'), new ui\FlexiBsGrid(new DataSource($evobj)));
$tabs->addTab(_('Structure'), new ui\BsEvidenceProperties($evobj));

$url = \constant('ABRAFLEXI_URL').'/c/'.\constant('ABRAFLEXI_COMPANY');
$evidence = $oPage->getRequestValue('evidence');

if ($evidence) {
    $url .= '/'.$evidence;
}

$method = $oPage->getRequestValue('method');
$body = $oPage->getRequestValue('body');

if (null === $body) {
    $body = $evobj->getJsonizedData([]);
}

$tabs->addTab(
    _('Query'),
    new \Ease\TWB5\Panel(
        _('Custom query'),
        'warning',
        new ui\SendForm($url, $method, $body),
    ),
);

$oPage->container->addItem($tabs);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
