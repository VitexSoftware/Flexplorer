<?php
/**
 * Flexplorer - Objekt uživatele.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
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
    public $flexiBee = null;

    /**
     * Sloupeček s loginem.
     *
     * @var string
     */
    public $nameColumn = 'login';

    /**
     * Uživatel autentizovaný vůči flexibee
     * @param int $userID
     */
    public function __construct($userID = null)
    {
        parent::__construct();
        $this->flexiBee = new \FlexiPeeHP\FlexiBeeRO();
//        $this->flexiBee->setEvidence('uzivatel');
    }

    /**
     * Perform logIn action
     * 
     * @param array $creds
     * @return boolean
     */
    public function tryToLogin($creds)
    {
        $loginStatus              = false;
        $this->flexiBee->disconnect();
        $this->flexiBee->user     = trim($creds['login']);
        $this->flexiBee->password = $creds['password'];
        $this->flexiBee->url      = trim($creds['server']);
        $this->flexiBee->company  = null;
        $this->flexiBee->prefix   = null;
        $this->flexiBee->curlInit();
        $companies                = $this->flexiBee->performRequest('c.json');
        if (isset($companies['companies'])) {
            if (isset($companies['companies']['company'][0]['dbNazev'])) {
                $this->flexiBee->company = $companies['companies']['company'][0]['dbNazev'];
            } else {
                $this->flexiBee->company = $companies['companies']['company']['dbNazev'];
            }

            $this->setMyKey(true);
            $loginStatus = $this->loginSuccess();
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
        $this->setDataValue($this->loginColumn, $this->flexiBee->user);
        $_SESSION['login']    = $this->flexiBee->user;
        $_SESSION['password'] = $this->flexiBee->password;
        $_SESSION['company']  = $this->flexiBee->company;
        $_SESSION['server']   = $this->flexiBee->url;
        return parent::loginSuccess();
    }
}