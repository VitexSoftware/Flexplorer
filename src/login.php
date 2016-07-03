<?php
/**
 * Flexplorer - Přihlašovací strana.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

if (!is_object($oUser)) {
    die(_('Cookies jsou vyžadovány'));
}

$login    = $oPage->getRequestValue('login');
$password = $oPage->getRequestValue('password');
$server   = $oPage->getRequestValue('server');
if ($login) {
    $oUser = \Ease\Shared::user(new User());
//    \Ease\Shared::user()->SettingsColumn = 'settings';
    if ($oUser->tryToLogin($_POST)) {
        $oPage->redirect('index.php');
        exit;
    }
} else {
    $forceID = $oPage->getRequestValue('force_id', 'int');
    if (!is_null($forceID)) {
        \Ease\Shared::user(new User($forceID));
        $oUser->setSettingValue('admin', true);
        $oUser->addStatusMessage(_('Přihlášen jako: ').$oUser->getUserLogin(),
            'success');
        \Ease\Shared::user()->loginSuccess();

        $backurl = $oPage->getRequestValue('backurl');
        if (!is_null($backurl)) {
            $oPage->redirect($backurl);
        } else {
            $oPage->redirect('main.php');
        }

        exit;
    } else {
        $oPage->addStatusMessage(_('Prosím potvrďte vaše přihlašovací udaje'));
    }
}

$oPage->addItem(new ui\PageTop(_('Přihlašení')));

$loginFace = new \Ease\Html\Div(null, ['id' => 'LoginFace']);

$oPage->container->addItem($loginFace);

$loginRow   = new \Ease\TWB\Row();
$infoColumn = $loginRow->addItem(new \Ease\TWB\Col(4));

$infoBlock = $infoColumn->addItem(new \Ease\TWB\Well(new \Ease\Html\ImgTag('images/password.png')));
$infoBlock->addItem(_('Vítejte'));

$loginColumn = $loginRow->addItem(new \Ease\TWB\Col(4));

$submit = new \Ease\TWB\SubmitButton(_('Přihlášení'), 'success');

$loginPanel = new \Ease\TWB\Panel(new \Ease\Html\ImgTag('images/flexplorer-logo.png'),
    'success', null, $submit);
$loginPanel->addItem(new \Ease\TWB\FormGroup(_('FlexiBee'),
    new \Ease\Html\InputTextTag('server',
    $server ? $server : constant('DEFAULT_FLEXIBEE_URL') ),
    constant('DEFAULT_FLEXIBEE_URL'),
    _('URL FlexiBee serveru. Např: https://localhost:5434')));
$loginPanel->addItem(new \Ease\TWB\FormGroup(_('Uživatelské jméno'),
    new \Ease\Html\InputTextTag('login',
    $login ? $login : constant('DEFAULT_FLEXIBEE_LOGIN')
    ), constant('DEFAULT_FLEXIBEE_LOGIN'), _('Jméno uživatele')));
$loginPanel->addItem(new \Ease\TWB\FormGroup(_('Heslo'),
    new \Ease\Html\InputPasswordTag('password',
    $password ? $password : constant('DEFAULT_FLEXIBEE_PASSWORD')),
    constant('DEFAULT_FLEXIBEE_PASSWORD'), _('Heslo uživatele')));

$loginColumn->addItem($loginPanel);

$oPage->container->addItem(new \Ease\TWB\Form('Login', null, 'POST', $loginRow));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
