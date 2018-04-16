<?php
/**
 * Flexplorer - Appplication menu.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class WebPage extends \Ease\TWB\WebPage
{
    public $requestURL = null;

    /**
     * Main block of page.
     *
     * @var \Ease\Html\DivTag
     */
    public $container = null;

    /**
     * First column.
     *
     * @var \Ease\Html\DivTag
     */
    public $columnI = null;

    /**
     * Druhý sloupec.
     *
     * @var \Ease\Html\DivTag
     */
    public $columnII = null;

    /**
     * Třetí sloupec.
     *
     * @var \Ease\Html\DivTag
     */
    public $columnIII = null;

    /**
     * Základní objekt stránky.
     *
     * @param VSUser $userObject
     */
    public function __construct($pageTitle = null, &$userObject = null)
    {
        if (is_null($userObject)) {
            $userObject = \Ease\Shared::user();
        }
        parent::__construct($pageTitle, $userObject);
        $this->includeCss('css/default.css');
        $this->head->addItem('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
        $this->head->addItem('<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="favicon.ico">');
        $this->head->addItem('<link rel="apple-touch-icon-precomposed"  type="image/png" href="images/flexplorer-logo.png">');
        $this->head->addItem('<link rel="stylesheet" href="/javascript/font-awesome/css/font-awesome.min.css">');

        $this->container = $this->addItem(new \Ease\TWB\Container());

        $this->includeJavaScript('js/jquery.keepAlive.js');
        $this->addJavaScript('$.fn.keepAlive({timer: 300000});');
    }

    /**
     * Only for admin.
     *
     * @param string $loginPage
     */
    public function onlyForAdmin($loginPage = 'login.php')
    {
        if (!$this->user->getSettingValue('admin')) {
            \Ease\Shared::user()->addStatusMessage(_('Please sign in as admin first'),
                'warning');
            $this->redirect($loginPage);
        }
    }

    /**
     * Nepřihlášeného uživatele přesměruje na přihlašovací stránku.
     *
     * @param string $loginPage adresa přihlašovací stránky
     * @param string $message Custom message for redirected
     */
    public function onlyForLogged($loginPage = 'login.php', $message = NULL)
    {
        if (!isset($_SESSION['backurl'])) {
            $_SESSION['backurl'] = $_SERVER['REQUEST_URI'];
        }
        return parent::onlyForLogged($loginPage, $message);
    }

    /**
     * Add given evidence to the top of history
     *
     * @param arrya $evidence
     */
    public function addEvidenceToHistory($evidence)
    {
        if (isset($_SESSION['evidence_history'])) {
            $newHistory = ['evidence.php?evidence='.$evidence => $evidence];
            foreach ($_SESSION['evidence_history'] as $link => $oldevidence) {
                if ($oldevidence != $evidence) {
                    $newHistory[$link] = $oldevidence;
                }
            }
            $_SESSION['evidence_history'] = $newHistory;
        } else {
            $_SESSION['evidence_history']['evidence.php?evidence='.$evidence] = $evidence;
        }
    }

    /**
     * Set URL of request to show 
     * @param string $url
     */
    public function setRequestURL($url)
    {
        $_SESSION['lasturl'] = $this->requestURL    = $url;
    }

    public function getRequestURL()
    {
        return is_null($this->requestURL) ? isset($_SESSION['lasturl']) ? $_SESSION['lasturl'] : '' : $this->requestURL;
    }

    /**
     * 
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

    public function draw()
    {
        if (\Ease\Shared::user()->getUserID()) { //Authenticated user
            $this->body->addAsFirst(new FlexiURL($this->getRequestURL(),
                    ['id' => 'lasturl', 'class' => 'innershadow']));
        }
        return parent::draw();
    }

    /**
     * Human readable size interpretation
     * 
     * @param long $a_bytes
     * 
     * @return string
     */
    static function formatBytes($a_bytes)
    {
        $a_bytes = doubleval($a_bytes);
        if ($a_bytes < 1024) {
            return $a_bytes.' B';
        } elseif ($a_bytes < 1048576) {
            return round($a_bytes / 1024, 2).' KiB';
        } elseif ($a_bytes < 1073741824) {
            return round($a_bytes / 1048576, 2).' MiB';
        } elseif ($a_bytes < 1099511627776) {
            return round($a_bytes / 1073741824, 2).' GiB';
        } elseif ($a_bytes < 1125899906842624) {
            return round($a_bytes / 1099511627776, 2).' TiB';
        } elseif ($a_bytes < 1152921504606846976) {
            return round($a_bytes / 1125899906842624, 2).' PiB';
        } elseif ($a_bytes < 1180591620717411303424) {
            return round($a_bytes / 1152921504606846976, 2).' EiB';
        } elseif ($a_bytes < 1208925819614629174706176) {
            return round($a_bytes / 1180591620717411303424, 2).' ZiB';
        } else {
            return round($a_bytes / 1208925819614629174706176, 2).' YiB';
        }
    }
}
