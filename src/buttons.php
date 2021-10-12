<?php

/**
 * Flexplorer - Buttons.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->addItem(new ui\PageTop(_('AbraFlexi Buttons')));

$evidenceButtonInfo = new \Ease\TWB\Panel(_('Evidence'), 'info',
        _('This button open current AbraFlexi evidence in FlexPlorer'),
        [new \Ease\TWB\LinkButton('getbuttonxml.php?type=evidence&operation=download',
            '<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
 ' . _('Download Buttons XML install file'), 'info'),
    new \Ease\TWB\LinkButton('getbuttonxml.php?type=evidence&operation=install',
            '<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
 ' . _('Install Buttons into AbraFlexi'), 'success')
        ]);

//$oPage->container->addItem( new \Ease\TWB\LinkButton('getbuttonxml.php?type=structure', _('Structure')) );
//$oPage->container->addItem( new \Ease\TWB\LinkButton('getbuttonxml.php?type=editor', _('Edit Record')) );
$oPage->container->addItem($evidenceButtonInfo);

//$oPage->container->addItem( new \Ease\TWB\LinkButton('getbuttonxml.php?type=webui', _('WebUI')) );

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
