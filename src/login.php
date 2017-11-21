<?php
/**
 * Flexplorer - Sign in page.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$login    = $oPage->getRequestValue('login');
$password = $oPage->getRequestValue('password');
$server   = $oPage->getRequestValue('server');
$backurl  = $oPage->getRequestValue('backurl');

if ($login) {
    $oUser = \Ease\Shared::user(new User());
    if ($oUser->tryToLogin($_REQUEST)) {
        if ($oPage->getRequestValue('remember-me')) {
            $_SESSION['bookmarks'][] = ['login' => $login, 'password' => $password,
            'server' => $server];
            $oPage->addStatusMessage(_('Server added to bookmarks'));
        }
        if (isset($_SESSION['backurl'])) {
            $oPage->redirect($_SESSION['backurl']);
            unset($_SESSION['backurl']);
        } else {
            $oPage->redirect('index.php');
        }
    }
} else {
    $forceID = $oPage->getRequestValue('force_id', 'int');
    if (!is_null($forceID)) {
        \Ease\Shared::user(new User($forceID));
        $oUser->setSettingValue('admin', true);
        $oUser->addStatusMessage(_('Signed in as: ').$oUser->getUserLogin(),
            'success');
        \Ease\Shared::user()->loginSuccess();

        if (!is_null($backurl)) {
            $oPage->redirect($backurl);
        } else {
            $oPage->redirect('index.php');
        }
    } else {
        $oPage->addStatusMessage(_('Please confirm your login credentials'));
    }
}

$oPage->addItem(new ui\PageTop(_('Sign in')));

$loginFace = new \Ease\Html\DivTag(null,['id' => 'LoginFace']);

$oPage->container->addItem($loginFace);

$loginRow   = new \Ease\TWB\Row();
$infoColumn = $loginRow->addItem(new \Ease\TWB\Col(4));

$infoBlock = $infoColumn->addItem(new \Ease\TWB\Well(new \Ease\Html\ImgTag('images/password.png')));
$infoBlock->addItem(_('Login Bookmarks'));

$_SESSION['bookmarks']['demo'] = ['login' => 'winstrom', 'password' => 'winstrom',
    'server' => 'https://demo.flexibee.eu'];

$_SESSION['bookmarks']['localhost'] = ['login' => '', 'password' => '',
    'server' => 'https://localhost:5434'];



$bookmarks = new \Ease\Html\UlTag(null, ['class' => 'list-group']);
    foreach ($_SESSION['bookmarks'] as $bookmark) {
        $bookmarks->addItemSmart(new \Ease\Html\ATag('login.php?login='.$bookmark['login'].'&password='.$bookmark['password'].'&server='.$bookmark['server'],
            str_replace('://', '://'.$bookmark['login'].'@', $bookmark['server'])),
            ['class' => 'list-group-item']);
    }
    $infoBlock->addItem($bookmarks);


$loginColumn = $loginRow->addItem(new \Ease\TWB\Col(4));

$submit = new \Ease\TWB\SubmitButton(_('Sign in'), 'success');

$loginPanel = new \Ease\TWB\Panel(new \Ease\Html\ImgTag('images/flexplorer-logo.png'),
    'success', null, $submit);
$loginPanel->addItem(new \Ease\TWB\FormGroup(_('FlexiBee'),
    new \Ease\Html\InputTextTag('server',
    $server ? $server : constant('DEFAULT_FLEXIBEE_URL') ),
    constant('DEFAULT_FLEXIBEE_URL'),
    _('FlexiBee server URL. ex.:').' <a href="?server=https://localhost:5434">https://localhost:5434</a>'));
$loginPanel->addItem(new \Ease\TWB\FormGroup(_('User name'),
    new \Ease\Html\InputTextTag('login',
    $login ? $login : constant('DEFAULT_FLEXIBEE_LOGIN')
    ), constant('DEFAULT_FLEXIBEE_LOGIN'), _('Login name')));
$loginPanel->addItem(new \Ease\TWB\FormGroup(_('Password'),
    new \Ease\Html\InputPasswordTag('password',
    $password ? $password : constant('DEFAULT_FLEXIBEE_PASSWORD')),
    constant('DEFAULT_FLEXIBEE_PASSWORD'), _('User\'s password')));
$loginPanel->addItem(new \Ease\TWB\FormGroup(_('Remeber me'),
    new ui\TWBSwitch('remember-me', true), null, _('Add this to Login History')));


$loginPanel->body->setTagCss(['margin' => '20px']);

$loginColumn->addItem($loginPanel);

$featureList = new \Ease\Html\UlTag(null, ['class' => 'list-group']);
$featureList->addItemSmart(_('display the contents of all the available records in all companies'),
    ['class' => 'list-group-item']);
$featureList->addItemSmart(_('show the structure of evidence'),
    ['class' => 'list-group-item']);
$featureList->addItemSmart(_('send direct requests to the server and display results'),
    ['class' => 'list-group-item']);
$featureList->addItemSmart(_('set up ChangesAPI and add WebHooks'),
    ['class' => 'list-group-item']);
$featureList->addItemSmart(_('Test WebHook script processing changes from FlexiBee answers'),
    ['class' => 'list-group-item']);
$featureList->addItemSmart(_('Collectively establish and abolish the accounting period'),
    ['class' => 'list-group-item']);
$featureList->addItemSmart(_('Evidnece distinguish which are inaccessible because of the license'),
    ['class' => 'list-group-item']);
$featureList->addItemSmart(_('Shown next to json result of the request and page FlexiBee'),
    ['class' => 'list-group-item']);
$featureList->addItemSmart(_('Edit External ID numbers'),
    ['class' => 'list-group-item']);

$featuresPanel = new \Ease\TWB\Panel(_('Features'), 'info');

\Ease\Page::addItemCustom($featureList, $featuresPanel);
$loginRow->addColumn(4, $featuresPanel);


$oPage->container->addItem(new \Ease\TWB\Form('Login', null, 'POST', $loginRow));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
