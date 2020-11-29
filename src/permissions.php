<?php

namespace Flexplorer;

/**
 * Flexplorer - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->addItem(new ui\PageTop(_('Permissions overview')));

$oPage->container->addItem(new ui\PermissionsViewer('download/permissions.csv'));

$oPage->container->addItem(new \Ease\TWB\Well([
            new \Ease\TWB\LinkButton('download/permissions.csv',
                    new \Ease\TWB\GlyphIcon('download') . ' ' . _('Get CSV'), 'info'),
            new \Ease\TWB\LinkButton('download/permissions.xlsx',
                    new \Ease\TWB\GlyphIcon('download') . ' ' . _('Get XLSx'), 'info'),
            new \Ease\TWB\LinkButton('download/permissions.html',
                    new \Ease\TWB\GlyphIcon('download') . ' ' . _('Get html'), 'info'),
        ]));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
