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
            $companer = new \FlexiPeeHP\Company();

            $infoLabel = str_replace('://',
                '://'.constant('FLEXIBEE_LOGIN').'@',
                $companer->getEvidenceURL());

            $infoLabel.= '/'.constant('FLEXIBEE_COMPANY');

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

            if (isset($companies['company']) && count($companies['company'])) {
                foreach ($companies['company'] as $company) {
                    $companiesToMenu['?company='.$company['dbNazev']] = $company['nazev'];
                }
                asort($companiesToMenu);

                $nav->addDropDownMenu(_('Firmy'), $companiesToMenu);

                if (!isset($_SESSION['company'])) { //Automaticky volíme první firmu
                    $_SESSION['company'] = $companies['company'][0]['dbNazev'];
                    define('FLEXIBEE_COMPANY', $_SESSION['company']);
                }


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
            }

            $nav->addDropDownMenu(_('Nástroje'),
                ['changesapi.php' => _('Changes API')]);
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