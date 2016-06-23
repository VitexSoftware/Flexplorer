<?php
/**
 * Flexplorer - stránka aplikace.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class WebPage extends \Ease\TWB\WebPage
{
    /**
     * Hlavní blok stránky.
     *
     * @var \Ease\Html\DivTag
     */
    public $container = null;

    /**
     * První sloupec.
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
        $this->IncludeCss('css/default.css');
        $this->head->addItem('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
        $this->head->addItem('<link rel="shortcut icon" type="image/svg+xml" href="images/logo.png">');
        $this->head->addItem('<link rel="apple-touch-icon-precomposed"  type="image/svg+xml" href="images/logo.png">');
        $this->head->addItem('<link rel="stylesheet" href="/javascript/font-awesome/css/font-awesome.min.css">');

        $this->container = $this->addItem(new \Ease\TWB\Container());
    }

    /**
     * Pouze pro admina.
     *
     * @param string $loginPage
     */
    public function onlyForAdmin($loginPage = 'login.php')
    {
        if (!$this->user->getSettingValue('admin')) {
            \Ease\Shared::user()->addStatusMessage(_('Nejprve se prosím přihlašte jako admin'),
                'warning');
            $this->redirect($loginPage);
            exit;
        }
    }

    /**
     * Nepřihlášeného uživatele přesměruje na přihlašovací stránku.
     *
     * @param string $loginPage adresa přihlašovací stránky
     */
    public function onlyForLogged($loginPage = 'login.php')
    {
        return parent::onlyForLogged($loginPage.'?backurl='.urlencode($_SERVER['REQUEST_URI']));
    }
}