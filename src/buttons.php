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

require_once 'includes/Init.php';

$oPage->addItem(new ui\PageTop(_('AbraFlexi Buttons')));

$evidenceButtonInfo = new \Ease\TWB5\Panel(
    _('Evidence'),
    'info',
    _('This button open current AbraFlexi evidence in FlexPlorer'),
    [new \Ease\TWB5\LinkButton(
        'getbuttonxml.php?type=evidence&operation=download',
        <<<'EOD'
<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>

EOD._('Download Buttons XML install file'),
        'info',
    ),
        new \Ease\TWB5\LinkButton(
            'getbuttonxml.php?type=evidence&operation=install',
            <<<'EOD'
<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>

EOD._('Install Buttons into AbraFlexi'),
            'success',
        ),
    ],
);

// $oPage->container->addItem( new \Ease\TWB5\LinkButton('getbuttonxml.php?type=structure', _('Structure')) );
// $oPage->container->addItem( new \Ease\TWB5\LinkButton('getbuttonxml.php?type=editor', _('Edit Record')) );
$oPage->container->addItem($evidenceButtonInfo);

// $oPage->container->addItem( new \Ease\TWB5\LinkButton('getbuttonxml.php?type=webui', _('WebUI')) );

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
