<?php
/**
 * Flexplorer - Menu aplikace.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class MainMenu extends \Ease\Html\Div
{

    /**
     * Vytvoří hlavní menu.
     */
    public function __construct()
    {
        parent::__construct(null, ['id' => 'MainMenu']);
    }

    /**
     * Data source.
     *
     * @param type   $source
     * @param string $icon   Description
     *
     * @return string
     */
    protected function getMenuList($source, $icon = '')
    {
        $keycolumn  = $source->getmyKeyColumn();
        $namecolumn = $source->nameColumn;
        $lister     = $source->getColumnsFromSQL([$source->getmyKeyColumn(), $namecolumn],
            [$keycolumn => true], $namecolumn, $keycolumn);

        $itemList = [];
        if ($lister) {
            foreach ($lister as $uID => $uInfo) {
                $itemList[$source->keyword.'.php?'.$keycolumn.'='.$uInfo[$keycolumn]]
                    = \Ease\TWB\Part::GlyphIcon($icon).'&nbsp;'.$uInfo[$namecolumn];
            }
        }

        return $itemList;
    }

    /**
     * Vložení menu.
     */
    public function afterAdd()
    {
        $nav = $this->addItem(new BootstrapMenu());

        $userID = \Ease\Shared::user()->getUserID();
        if ($userID) { //Authenticated user
            $infoLabel = str_replace('://',
                '://'.constant('FLEXIBEE_LOGIN').'@',
                constant('FLEXIBEE_URL').'/c/'.constant('FLEXIBEE_COMPANY'));

            $evidence = $this->webPage->getRequestValue('evidence');
            if ($evidence) {
                $infoLabel.='/'.$evidence;
            }

            $nav->addMenuItem(new \Ease\Html\Div(new \Ease\TWB\Label('success',
                new \Ease\Html\ATag($infoLabel, $infoLabel),
                ['class' => 'navbar-text', 'style' => 'color: yellow; font-size: 12px;']),
                ['class' => 'collapse navbar-collapse']));

            $companiesToMenu = [];
            $companer        = new \FlexiPeeHP\Company();
            $companies       = $companer->getFlexiData();

            if (count($companies)) {
                foreach ($companies['company'] as $company) {
                    $companiesToMenu['?company='.$company['dbNazev']] = $company['nazev'];
                }
                asort($companiesToMenu);
            }


            $nav->addDropDownMenu(_('Firmy'), $companiesToMenu);

            $lister    = new \FlexiPeeHP\EvidenceList();
            $flexidata = $lister->getFlexiData();

            if (count($flexidata)) {
                foreach ($flexidata['evidences']['evidence'] as $evidence) {
                    $evidenciesToMenu['evidence.php?evidence='.$evidence['evidencePath']]
                        = $evidence['evidenceName'];
                }
                asort($evidenciesToMenu);
            }


            $nav->addDropDownMenu(_('Evidence'), $evidenciesToMenu);

//            $nav->addMenuItem(new \Ease\TWB\LinkButton('invoice.php',
//                _('Faktura')));
        }
    }

    /**
     * Přidá do stránky javascript pro skrývání oblasti stavových zpráv.
     */
    public function finalize()
    {
        $this->addCss('body {
                padding-top: 60px;
                padding-bottom: 40px;
            }');

        \Ease\JQuery\Part::jQueryze($this);
        \Ease\Shared::webPage()->addCss('.dropdown-menu { overflow-y: auto } ');
        \Ease\Shared::webPage()->addJavaScript("$('.dropdown-menu').css('max-height',$(window).height()-100);",
            null, true);
        $this->includeJavaScript('js/slideupmessages.js');
    }
}