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

namespace Flexplorer\ui;

class PageBottom extends \Ease\TWB5\Container
{
    public const BUILD = '';

    public function __construct($content = null)
    {
        parent::__construct($content);
        $this->setTagID('footer');

        if (method_exists('Composer\InstalledVersions', 'getRootPackage')) {
            $composer = \Composer\InstalledVersions::getRootPackage()['install_path'].'/composer.lock';
        } else {
            $composer = '../composer.lock';
        }

        $rowFluid1 = new \Ease\TWB5\Row();
        $colA = $rowFluid1->addItem(new \Ease\TWB5\Col(2));
        $listA1 = $colA->addItem(new \Ease\Html\UlTag(
            _('Sources'),
            ['style' => 'list-style-type: none'],
        ));
        $listA1->addItemSmart(new \Ease\Html\ATag(
            'https://github.com/VitexSoftware/Flexplorer',
            'Flexiplorer',
        ));
        $listA1->addItemSmart(new \Ease\Html\ATag(
            'https://github.com/Spoje-NET/AbraFlexi',
            'AbraFlexi',
        ));
        $listA1->addItemSmart(new \Ease\Html\ATag(
            'https://github.com/VitexSoftware/EaseFramework',
            'PHP Ease Framework',
        ));

        $colB = $rowFluid1->addItem(new \Ease\TWB5\Col(2));
        $listB1 = $colB->addItem(new \Ease\Html\UlTag(
            _('Support'),
            ['style' => 'list-style-type: none'],
        ));
        $listB1->addItemSmart(new \Ease\Html\ATag(
            'https://www.flexibee.eu/podpora/Tickets/ViewList',
            'My issues',
        ));
        $listB1->addItemSmart(new \Ease\Html\ATag(
            'https://www.flexibee.eu/podpora/Tickets/Submit',
            'Enter issue',
        ));

        $colC = $rowFluid1->addItem(new \Ease\TWB5\Col(2));
        $listC1 = $colC->addItem(new \Ease\Html\UlTag(
            _('Services'),
            ['style' => 'list-style-type: none'],
        ));
        $listC1->addItemSmart(new \Ease\Html\ATag(
            'https://www.flexibee.eu/podpora/stazeni-abraflexi/',
            'Download',
        ));
        $listC1->addItemSmart(new \Ease\Html\ATag(
            'https://www.flexibee.eu/api/licence-pro-vyvojare/zadost-o-vyvojarskou-licenci/',
            'Deverloper License request',
        ));

        $colD = $rowFluid1->addItem(new \Ease\TWB5\Col(2));
        $listD1 = $colD->addItem(new \Ease\Html\UlTag(
            _('Docs'),
            ['style' => 'list-style-type: none'],
        ));
        $listD1->addItemSmart(new \Ease\Html\ATag(
            'https://demo.flexibee.eu/devdoc/',
            'REST API',
        ));

        $colE = $rowFluid1->addItem(new \Ease\TWB5\Col(2));
        $listE1 = $colE->addItem(new \Ease\Html\UlTag(_('Author'), ['style' => 'list-style-type: none']));
        $listE1->addItemSmart(new \Ease\Html\ATag('http://vitexsoftware.com/', _('Vitex Software')));

        $colF = $rowFluid1->addItem(new \Ease\TWB5\Col(2));
        $listF1 = $colF->addItem(new \Ease\Html\UlTag(_('Sponsored by'), ['style' => 'list-style-type: none']));
        $listF1->addItemSmart(new \Ease\Html\ATag('http://www.spoje.net/firma/o-nas/', _('Spoje.Net')));

        $this->addItem($rowFluid1);

        $rowFluid2 = new \Ease\TWB5\Row();

        $rowFluid2->addItem([new \Ease\TWB5\Col(8, '<strong>FlexPlorer</strong> '.\Ease\Shared::appVersion().(empty(self::BUILD) ? '' : '&nbsp;'._('build').' #'.self::BUILD).'<br>'._('the age of the installation').'&nbsp;'.new \Ease\Html\Widgets\LiveAge((new \DateTime())->setTimestamp(filemtime($composer)))), new \Ease\TWB5\Col(4, _('&copy; 2016-2025 Vítězslav "Vitex" Dvořák'))]);

        $this->addItem($rowFluid2);
    }

    /**
     * Finalize page bottom.
     */
    public function finalize(): void
    {
        $webPage = \Ease\WebPage::singleton();

        if (isset($webPage->heroUnit) && !\count($webPage->heroUnit->pageParts)) {
            unset($webPage->container->pageParts['\Ease\Html\DivTag@heroUnit']);
        }

        $this->includeCss('/javascript/font-awesome/css/font-awesome.min.css');
        parent::finalize();
    }
}
