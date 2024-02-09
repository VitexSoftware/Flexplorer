<?php

/**
 * Flexplorer - Objekt uživatele.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

/**
 * Description of User
 *
 * @author vitex
 */
class User extends \Ease\User
{
    /**
     * AbraFlexi engine
     * @var \AbraFlexi\RO
     */
    public $abraFlexi = null;

    /**
     * Sloupeček s loginem.
     *
     * @var string
     */
    public $nameColumn = 'login';

    /**
     * Uživatel autentizovaný vůči abraflexi
     * @param int $userID
     */
    public function __construct($userID = null)
    {
        parent::__construct();
        $this->abraFlexi = new \AbraFlexi\RO();
//        $this->abraFlexi->setEvidence('uzivatel');
    }

    /**
     * Perform logIn action
     *
     * @param array $creds
     *
     * @return boolean
     */
    public function tryToLogin($creds)
    {
        $loginStatus = false;
        $this->abraFlexi->disconnect();
        $this->abraFlexi->user = trim($creds['login']);

        $this->abraFlexi->password = $creds['password'];
        $this->abraFlexi->url = trim($creds['server']);
        $this->abraFlexi->company = null;
        $this->abraFlexi->prefix = null;
        $this->abraFlexi->curlInit();

        try {
            $companies = $this->abraFlexi->performRequest('c.json');
            if (isset($companies['companies'])) {
                if (isset($companies['companies']['company'])) {
                    $this->abraFlexi->company = array_key_exists('dbNazev', $companies['companies']['company']) ? $companies['companies']['company']['dbNazev'] : end($companies['companies']['company'])['dbNazev'];
                } else {
                    $this->abraFlexi->company = $companies['companies']['company']['dbNazev'];
                }
                $this->setMyKey(true);
                $loginStatus = $this->loginSuccess();
            } else {
                $this->addStatusMessage(_('Login Failed'), 'warning');
            }
        } catch (\AbraFlexi\Exception $exc) {
            $this->addStatusMessage($exc->getMessage(), 'error');
        }

        return $loginStatus;
    }

    /**
     * Provede přihlášení uživatele
     *
     * @return type
     */
    public function loginSuccess()
    {
        $_SESSION['user'] = $this->abraFlexi->user;
        $_SESSION['password'] = $this->abraFlexi->password;
        $_SESSION['url'] = $this->abraFlexi->url;
        $_SESSION['company'] = $this->abraFlexi->company;

        $this->abraFlexi->setEvidence('');
        $this->abraFlexi->setCompany('');
        $licenseInfo = $this->abraFlexi->performRequest('default-license.json');
        $_SESSION['license'] = $licenseInfo['license'];

        return parent::loginSuccess();
    }

    /**
     * Common instance of User class
     *
     * @return User
     */
    public static function singleton($user = null)
    {
        if (!isset(self::$instance)) {
            self::$instance = is_null($user) ? new self() : $user;
        }
        return self::$instance;
    }

    public function __wakeup()
    {
        $this->abraFlexi = new \AbraFlexi\RO();
    }
}
