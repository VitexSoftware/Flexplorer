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
    /**
     * FlexiBee engine
     * @var \FlexiPeeHP\FlexiBeeRO
     */
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
        $loginStatus          = false;
        $this->flexiBee->disconnect();
        $this->flexiBee->user = trim($creds['login']);

        $this->flexiBee->password = $creds['password'];
        $this->flexiBee->url      = trim($creds['server']);
        $this->flexiBee->company  = null;
        $this->flexiBee->prefix   = null;
        $this->flexiBee->curlInit();
        $companies                = $this->flexiBee->performRequest('c.json');
        if (isset($companies['companies'])) {
            if (isset($companies['companies']['company'])) {
                $this->flexiBee->company = array_key_exists('dbNazev', $companies['companies']['company'] ) ? $companies['companies']['company']['dbNazev'] : end($companies['companies']['company'])['dbNazev'];
            } else {
                $this->flexiBee->company = $companies['companies']['company']['dbNazev'];
            }
            $this->setMyKey(true);
            $loginStatus = $this->loginSuccess();
        } else {
            $this->addStatusMessage(_('Login Failed'), 'warning');
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
        $_SESSION['user']     = $this->flexiBee->user;
        $_SESSION['password'] = $this->flexiBee->password;
        $_SESSION['url']      = $this->flexiBee->url;
        $_SESSION['company']  = $this->flexiBee->company;

        $this->flexiBee->setEvidence('');
        $this->flexiBee->setCompany('');
        $licenseInfo         = $this->flexiBee->performRequest('default-license.json');
        $_SESSION['license'] = $licenseInfo['license'];


        return parent::loginSuccess();
    }
}
