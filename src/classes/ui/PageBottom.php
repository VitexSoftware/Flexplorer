<?php
/**
 * Flexplorer - spodek stránky.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class PageBottom extends \Ease\TWB\Container
{

    public function __construct($content = null)
    {
        parent::__construct($content);
        $this->SetTagID('footer');
        $this->addItem('<hr>');

        $composer = 'composer.json';
        if (!file_exists($composer)) {
            $composer = '../'.$composer;
        }

        $appInfo = json_decode(file_get_contents($composer));


        $rowFluid1 = new \Ease\TWB\Row();
        $colA      = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $listA1    = $colA->addItem(new \Ease\Html\UlTag(_('Sources'),
            ['style' => 'list-style-type: none']));
        $listA1->addItemSmart(new \Ease\Html\ATag('https://github.com/VitexSofware/Flexplorer',
            'Flexiplorer'));
        $listA1->addItemSmart(new \Ease\Html\ATag('https://github.com/Spoje-NET/FlexiPeeHP',
            'FlexiPeeHP'));
        $listA1->addItemSmart(new \Ease\Html\ATag('https://github.com/VitexSoftware/EaseFramework',
            'PHP Ease Framework'));

        $colB   = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $listB1 = $colB->addItem(new \Ease\Html\UlTag(_('Support'),
            ['style' => 'list-style-type: none']));
        $listB1->addItemSmart(new \Ease\Html\ATag('https://www.flexibee.eu/podpora/Tickets/ViewList',
            'My issues'));
        $listB1->addItemSmart(new \Ease\Html\ATag('https://www.flexibee.eu/podpora/Tickets/Submit',
            'Enter issue'));

        $colC   = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $listC1 = $colC->addItem(new \Ease\Html\UlTag(_('Services'),
            ['style' => 'list-style-type: none']));
        $listC1->addItemSmart(new \Ease\Html\ATag('https://www.flexibee.eu/podpora/stazeni-flexibee/',
            'Download'));
        $listC1->addItemSmart(new \Ease\Html\ATag('https://www.flexibee.eu/api/licence-pro-vyvojare/zadost-o-vyvojarskou-licenci/',
            'Deverloper License request'));

        $colD   = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $listD1 = $colD->addItem(new \Ease\Html\UlTag(_('Docs'),
            ['style' => 'list-style-type: none']));
        $listD1->addItemSmart(new \Ease\Html\ATag('https://demo.flexibee.eu/devdoc/',
            'REST API'));

        $colE   = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $listE1 = $colE->addItem(new \Ease\Html\UlTag(_('Autor'),
            ['style' => 'list-style-type: none']));
        $listE1->addItemSmart(new \Ease\Html\ATag('http://vitexsoftware.cz',
            _('Vitex Software')));

        $colF   = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $listF1 = $colF->addItem(new \Ease\Html\UlTag(_('Sponsored by'),
            ['style' => 'list-style-type: none']));
        $listF1->addItemSmart(new \Ease\Html\ATag('http://www.spoje.net/firma/o-nas/',
            _('Spoje.Net')));

        $this->addItem($rowFluid1);

        $rowFluid2 = new \Ease\TWB\Row();

        $rowFluid2->addItem(new \Ease\TWB\Col(12,
            [new \Ease\TWB\Col(8,
                'Flex<strong>plorer</strong> v.: '.$appInfo->version
            ), new \Ease\TWB\Col(4,
        _('&copy; 2016 Vítězslav "Vitex" Dvořák'))]));

        $this->addItem($rowFluid2);
    }

    /**
     * Zobrazí přehled právě přihlášených a spodek stránky.
     */
    public function finalize()
    {
        if (isset($this->webPage->heroUnit) && !count($this->webPage->heroUnit->pageParts)) {
            unset($this->webPage->container->pageParts['\Ease\Html\DivTag@heroUnit']);
        }

        $this->includeCss('/javascript/font-awesome/css/font-awesome.min.css');
    }
}