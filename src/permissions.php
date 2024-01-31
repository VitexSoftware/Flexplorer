<?php

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

$oPage->container->addItem(new ui\PermissionsViewer('download/permissions.csv'));

$oPage->container->addItem(new \Ease\TWB5\Well([
            new \Ease\TWB5\LinkButton('download/permissions.csv',
                    new \Ease\TWB5\GlyphIcon('download') . ' ' . _('Get CSV'), 'info'),
            new \Ease\TWB5\LinkButton('download/permissions.xlsx',
                    new \Ease\TWB5\GlyphIcon('download') . ' ' . _('Get XLSx'), 'info'),
            new \Ease\TWB5\LinkButton('download/permissions.html',
                    new \Ease\TWB5\GlyphIcon('download') . ' ' . _('Get html'), 'info'),
        ]));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
