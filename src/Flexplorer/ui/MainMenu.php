<?php

/**
 * Flexplorer - Application Menu.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class MainMenu extends \Ease\Html\DivTag {

    /**
     * MainMenu.
     */
    public function __construct() {
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
    protected function getMenuList($source, $icon = '') {
        $keycolumn = $source->getkeyColumn();
        $namecolumn = $source->nameColumn;
        $lister = $source->getColumnsFromSQL([$source->getkeyColumn(), $namecolumn],
                [$keycolumn => true], $namecolumn, $keycolumn);

        $itemList = [];
        if ($lister) {
            foreach ($lister as $uID => $uInfo) {
                $itemList[$source->keyword . '.php?' . $keycolumn . '=' . $uInfo[$keycolumn]] = \Ease\TWB\Part::GlyphIcon($icon) . '&nbsp;' . $uInfo[$namecolumn];
            }
        }

        return $itemList;
    }

    /**
     * Vložení menu.
     */
    public function afterAdd() {
        $nav = $this->addItem(new BootstrapMenu());
        $webPage = WebPage::singleton();
        $myCompany = isset($_SESSION['company']) ? $_SESSION['company'] : '';
        $userID = \Ease\Shared::user()->getUserID();
        if ($userID) { //Authenticated user
            if (isset($_SESSION['searchQuery'])) {
                $term = $_SESSION['searchQuery'];
            } else {
                $term = null;
            }

            $nav->addMenuItem(new NavBarSearchBox('search', 'search.php', $term));
            $companer = new \AbraFlexi\Company(null, ['company' => null]);

            $url = $webPage->getRequestValue('url');
            if (is_null($url)) {
                $infoLabel = $companer->getEvidenceURL();

                $infoLabel .= $myCompany;

                $evidence = $webPage->getRequestValue('evidence');
                if ($evidence) {
                    $infoLabel .= '/' . $evidence;
                }
            } else {
                $infoLabel = $url;
            }
            $nav->addMenuItem(new \Ease\Html\DivTag(new \Ease\TWB\Label('success',
                                    new \Ease\Html\ATag($infoLabel, $infoLabel),
                                    ['class' => 'navbar-text', 'style' => 'color: yellow; font-size: 12px; max-width: 800px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;']),
                            ['class' => 'collapse navbar-collapse']));

            $companiesToMenu = [];

            $companies = $companer->getFlexiData();

            if (isset($companies) && count($companies)) {
                foreach ($companies as $company) {
                    $companiesToMenu['company.php?company=' . $company['dbNazev']] = $company['nazev'];
                }
                asort($companiesToMenu);

                $companyTools = [
                    'newcompany.php' => new \Ease\TWB\GlyphIcon('plus') . ' ' . _('New'),
                    'companies.php' => new \Ease\TWB\GlyphIcon('list') . ' ' . _('Listing'),
                    '' => ''
                ];

                $nav->addDropDownMenu(_('Company'),
                        array_merge($companyTools, $companiesToMenu));

                if (!isset($_SESSION['company'])) { //Auto choose first company
                    $_SESSION['company'] = $companies[0]['dbNazev'];
                    define('ABRAFLEXI_COMPANY', $_SESSION['company']);
                }

                if (!array_key_exists('evidence-menu', $_SESSION)) {
                    $_SESSION['evidence-menu'] = [];
                }

                if (!array_key_exists($_SESSION['company'], $companiesToMenu)) {

                    $lister = new \AbraFlexi\EvidenceList(null, $_SESSION);
                    $evidences = $lister->getFlexiData();

                    if (count($evidences)) {
                        foreach ($evidences as $evidence) {
                            $evidenciesToMenu['evidence.php?evidence=' . $evidence['evidencePath']] = $evidence['evidenceName'];
                        }
                        asort($evidenciesToMenu);
                        $_SESSION['evidence-menu'][$_SESSION['company']] = $evidenciesToMenu;
                    } else {
                        $lister->addStatusMessage(_('Loading evidence list failed'),
                                'error');
                    }
                }


                if (array_key_exists('', $companiesToMenu)) {
                    
                }

                $evidenciesToMenu = array_merge(['evidences.php' => _('Overview')],
                        WebPage::singleton()->getEvidenceHistory(),
                        $_SESSION['evidence-menu'][$_SESSION['company']]);

                if (count($evidenciesToMenu)) {
                    $nav->addDropDownMenu(_('Evidence'), $evidenciesToMenu);
                }
            }


            $nav->addDropDownMenu(_('Tools'),
                    [
                        'query.php' => _('Query'),
//                'xslt.php' => _('XSLT'),
                        'buttons.php' => _('Buttons'),
                        'changesapi.php' => _('Changes API'),
                        'changes.php' => _('Changes Recieved'),
                        'fakechange.php' => _('WebHook test'),
                        'ucetniobdobi.php' => _('Accounting period'),
                        'permissions.php' => _('Role Permissions'),
                        'backups.php' => _('Backups')
            ]);
        }
    }

    /**
     * Přidá do stránky javascript pro skrývání oblasti stavových zpráv.
     */
    public function finalize() {
        $this->addCss('body {
                padding-top: 60px;
                padding-bottom: 40px;
            }');

        if (!empty(\Ease\Shared::logger()->getMessages())) {

            WebPage::singleton()->addCss('
#smdrag { height: 8px; 
          background-image:  url( images/slidehandle.png ); 
          background-color: #ccc; 
          background-repeat: no-repeat; 
          background-position: top center; 
          cursor: ns-resize;
}
#smdrag:hover { background-color: #f5ad66; }

');

            $this->addItem(WebPage::singleton()->getStatusMessagesBlock(['id' => 'status-messages', 'title' => _('Click to hide messages')]));
            $this->addItem(new \Ease\Html\DivTag(null, ['id' => 'smdrag', 'style' => 'margin-bottom: 5px']));
            \Ease\Shared::logger()->cleanMessages();
            WebPage::singleton()->addCss('.dropdown-menu { overflow-y: auto } ');
            WebPage::singleton()->addJavaScript("$('.dropdown-menu').css('max-height',$(window).height()-100);",
                    null, true);
            WebPage::singleton()->includeJavaScript('js/slideupmessages.js');
        }
    }

}
