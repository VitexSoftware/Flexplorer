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

class WebPage extends \Ease\TWB5\WebPage
{
    
    /**
     * Where to look for bootstrap stylesheet.
     *
     * @var string path or url
     */
    public string $bootstrapCSS = 'css/bootstrap.min.css';
    
    public string $requestURL = '';

    /**
     * Main block of page.
     */
    public \Ease\Html\DivTag $container;

    /**
     * First column.
     */
    public \Ease\Html\DivTag $columnI;

    /**
     * Druhý sloupec.
     */
    public \Ease\Html\DivTag $columnII;

    /**
     * Třetí sloupec.
     */
    public \Ease\Html\DivTag $columnIII;

    /**
     * Základní objekt stránky.
     *
     * @param string $pageTitle
     */
    public function __construct($pageTitle = '')
    {
        parent::__construct($pageTitle);
        \Ease\Part::jQueryze();

        $this->includeCss('css/default.css');
        $this->head->addItem('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
        $this->head->addItem('<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="favicon.ico">');
        $this->head->addItem('<link rel="apple-touch-icon-precomposed"  type="image/png" href="images/flexplorer-logo.png">');
        $this->head->addItem('<link rel="stylesheet" href="/javascript/font-awesome/css/font-awesome.min.css">');

        $this->container = $this->addItem(new \Ease\TWB5\Container());
    }

    /**
     * Only for admin.
     *
     * @param string $loginPage
     */
    public function onlyForAdmin($loginPage = 'login.php'): void
    {
        if (!$this->user->getSettingValue('admin')) {
            \Ease\Shared::user()->addStatusMessage(_('Please sign in as admin first'),'warning',);
            $this->redirect($loginPage);
        }
    }

    /**
     * Nepřihlášeného uživatele přesměruje na přihlašovací stránku.
     *
     * @param string $loginPage adresa přihlašovací stránky
     * @param string $message   Custom message for redirected
     */
    public function onlyForLogged($loginPage = 'login.php', $message = null)
    {
        if (!isset($_SESSION['backurl'])) {
            $_SESSION['backurl'] = $_SERVER['REQUEST_URI'];
        }

        return parent::onlyForLogged($loginPage, $message);
    }

    /**
     * Add given evidence to the top of history.
     *
     * @param arrya $evidence
     */
    public function addEvidenceToHistory($evidence): void
    {
        if (isset($_SESSION['evidence_history'])) {
            $newHistory = ['evidence.php?evidence='.$evidence => $evidence];

            foreach ($_SESSION['evidence_history'] as $link => $oldevidence) {
                if ($oldevidence !== $evidence) {
                    $newHistory[$link] = $oldevidence;
                }
            }

            $_SESSION['evidence_history'] = $newHistory;
        } else {
            $_SESSION['evidence_history']['evidence.php?evidence='.$evidence] = $evidence;
        }
    }

    /**
     * Set URL of request to show.
     *
     * @param string $url
     */
    public function setRequestURL($url): void
    {
        $_SESSION['lasturl'] = $this->requestURL = $url;
    }

    public function getRequestURL()
    {
        return null === $this->requestURL ? $_SESSION['lasturl'] ?? '' : $this->requestURL;
    }

    /**
     * @return type
     */
    public function getEvidenceHistory()
    {
        if (!empty($_SESSION['evidence_history'])) {
            $history = array_merge([''], $_SESSION['evidence_history'], ['']);
        } else {
            $history = [''];
        }

        return $history;
    }

    public function finalize(): void
    {
        if ($this->finalized === false) {
            if (\Ease\Shared::user()->getUserID()) { // Authenticated user
                $urlPanel = new \Ease\Html\DivTag(null,['style'=>'height: 50px; margin-top: 50px; margin-bottom: 20px;']);
                $urlPanel->addItem(new FlexiURL(self::singleton()->getRequestURL(), ['id' => 'lasturl', 'class' => 'innershadow']));
                $this->body->addAsFirst($urlPanel);
            }

            $this->includeJavaScript('js/jquery.keepAlive.js');
            $this->addJavaScript('$.fn.keepAlive({timer: 300000});');

            parent::finalize();
        }
    }
}
