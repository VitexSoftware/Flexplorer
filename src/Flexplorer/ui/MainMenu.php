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

/**
 * Description of MainMenu.
 *
 * @author     Vitex <vitex@hippy.cz>
 */
class MainMenu extends \Ease\TWB5\Navbar
{
    /**
     * Menu aplikace.
     *
     * @param string $brand
     * @param array  $properties
     */
    public function __construct(string $name, $brand, $properties = [])
    {
        parent::__construct($brand, $name, $properties);
        $this->addTagClass('navbar-inverse bg-inverse navbar-toggleable-sm  navbar-expand-lg bg-secondary text-uppercase fixed-top');

        $myCompany = $_SESSION['company'] ?? '';
        $userID = \Ease\Shared::user()->getUserID();

        if ($userID) { // Authenticated user
            if (isset($_SESSION['searchQuery'])) {
                $term = $_SESSION['searchQuery'];
            } else {
                $term = null;
            }

            //            $this->addMenuItem(new NavBarSearchBox('search', 'search.php', $term));
            $companer = new \AbraFlexi\Company(null, ['company' => null]);

            $url = WebPage::getRequestValue('url');

            if (null === $url) {
                $infoLabel = $companer->getEvidenceURL();

                $infoLabel .= $myCompany;

                $evidence = WebPage::getRequestValue('evidence');

                if ($evidence) {
                    $infoLabel .= '/'.$evidence;
                }
            } else {
                $infoLabel = $url;
            }

            //            $nav->addMenuItem(new \Ease\Html\DivTag(new \Ease\TWB5\Label('success',
            //                                    new \Ease\Html\ATag($infoLabel, $infoLabel),
            //                                    ['class' => 'navbar-text', 'style' => 'color: yellow; font-size: 12px; max-width: 800px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;']),
            //                            ['class' => 'collapse navbar-collapse']));

            $companiesToMenu = [];

            $companies = $companer->getFlexiData();

            foreach ($companies as $company) {
                $companiesToMenu['company.php?company='.$company['dbNazev']] = $company['nazev'];
            }

            asort($companiesToMenu);

            $companyTools = [
                'newcompany.php' => '➕ '._('New'),
                'companies.php' => '🏭 '._('Listing'),
                '' => '',
            ];

            $this->addDropDownMenu('🏭 '._('Company'), array_merge($companyTools, $companiesToMenu));

            if (!isset($_SESSION['company'])) { // Auto choose first company
                $_SESSION['company'] = $companies[0]['dbNazev'];
                \define('ABRAFLEXI_COMPANY', $_SESSION['company']);
            }

            if (!\array_key_exists('evidence-menu', $_SESSION)) {
                $_SESSION['evidence-menu'] = [];
            }

            if (!\array_key_exists($_SESSION['company'], $companiesToMenu)) {
                $lister = new \AbraFlexi\EvidenceList(null, $_SESSION);
                $evidences = $lister->getFlexiData();

                if (\count($evidences)) {
                    foreach ($evidences as $evidence) {
                        $evidenciesToMenu['evidence.php?evidence='.$evidence['evidencePath']] = $evidence['evidenceName'];
                    }

                    asort($evidenciesToMenu);
                    $_SESSION['evidence-menu'][$_SESSION['company']] = $evidenciesToMenu;
                } else {
                    $lister->addStatusMessage(_('Loading evidence list failed'), 'error');
                }
            }

            if (\array_key_exists('', $companiesToMenu)) {
            }

            $evidenciesToMenu = array_merge(
                ['evidences.php' => _('Overview')],
                WebPage::singleton()->getEvidenceHistory(),
                $_SESSION['evidence-menu'][$_SESSION['company']],
            );

            if (\count($evidenciesToMenu)) {
                $this->addDropDownMenu('🗃️ '._('Evidence'), $evidenciesToMenu);
            }

            $this->addDropDownMenu(
                '🛠️ '._('Tools'),
                [
                    'query.php' => _('Query'),
                    //                'xslt.php' => _('XSLT'),
                    'buttons.php' => _('Buttons'),
                    'changesapi.php' => _('Changes API'),
                    'changes.php' => _('Changes Recieved'),
                    'fakechange.php' => _('WebHook test'),
                    'ucetniobdobi.php' => _('Accounting period'),
                    'permissions.php' => _('Role Permissions'),
                    'backups.php' => _('Backups'),
                ],
            );
            $this->addMenuItem(new \Ease\Html\ATag('logout.php', '🚪 '._('Sign off')));
        }
    }
}
