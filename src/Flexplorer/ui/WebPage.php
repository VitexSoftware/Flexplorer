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
     * Bump when css/vitex.css changes, so browsers fetch the new version.
     */
    public const ASSET_VERSION = '1.0.0';

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

        $this->head->addItem('<script>(function(){var t="dark";try{t=localStorage.getItem("vsTheme")||t}catch(e){}var d=document.documentElement;d.setAttribute("data-theme",t);d.setAttribute("data-bs-theme",t)})()</script>');
        $this->head->addItem('<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>');
        $this->includeCss('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600&family=Caveat:wght@400;500&display=swap');
        $this->includeCss('css/default.css');
        $this->includeCss('css/vitex.css?v='.self::ASSET_VERSION);
        $this->head->addItem('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
        $this->head->addItem('<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="favicon.ico">');
        $this->head->addItem('<link rel="apple-touch-icon-precomposed"  type="image/png" href="images/flexplorer-logo.png">');
        $this->head->addItem('<link rel="stylesheet" href="/javascript/font-awesome/css/font-awesome.min.css">');
    }

    /**
     * Only for admin.
     *
     * @param string $loginPage
     */
    public function onlyForAdmin($loginPage = 'login.php'): void
    {
        if (!$this->user->getSettingValue('admin')) {
            \Ease\Shared::user()->addStatusMessage(_('Please sign in as admin first'), 'warning');
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
            $this->includeJavaScript('js/jquery.keepAlive.js');
            $this->addJavaScript('$.fn.keepAlive({timer: 300000});');

            parent::finalize();
        }
    }
}
