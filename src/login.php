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

namespace Flexplorer;

use Ease\Html\DivTag;
use Ease\Html\Form;
use Ease\Html\ImgTag;
use Ease\Html\InputPasswordTag;
use Ease\Html\InputTextTag;
use Ease\Shared;
use Ease\TWB5\Card;
use Ease\TWB5\Col;
use Ease\TWB5\InputGroup;
use Ease\TWB5\Panel;
use Ease\TWB5\Row;
use Ease\TWB5\SubmitButton;
use Flexplorer\ui\PageBottom;
use Flexplorer\ui\PageTop;

require_once 'includes/Init.php';
$oPage->addItem(new PageTop(_('Sign in')));

$oPage->addAsFirst(new \Ease\Html\ImgTag('images\flexibeetop.png', 'top', ['class' => 'img-fluid', 'width' => '100%']));

// $loginFace = new \Ease\Html\DivTag(null, ['id' => 'LoginFace']);
//
// $oPage->container->addItem($loginFace);
//
// $loginRow = new \Ease\TWB5\Row();
// $infoColumn = $loginRow->addItem(new \Ease\TWB5\Col(4));
//
// $infoBlock = $infoColumn->addItem(new \Ease\TWB5\Well(new \Ease\Html\ImgTag('images/password.png')));
// $infoBlock->addItem(_('Login Bookmarks'));
//
// $_SESSION['bookmarks']['demo'] = ['login' => 'winstrom', 'password' => 'winstrom',
//    'server' => 'https://demo.flexibee.eu', 'comapny' => 'demo'];
//
// $_SESSION['bookmarks']['localhost'] = ['login' => '', 'password' => '',
//    'server' => 'https://localhost:5434'];
//
// if (is_dir(\Ease\Shared::cfg('CONFIGS'))) {
//    foreach (scandir(\Ease\Shared::cfg('CONFIGS')) as $candidat) {
//        if ($candidat[0] == '.') {
//            continue;
//        }
//        if (strtolower(pathinfo($candidat, PATHINFO_EXTENSION)) == 'json') {
//            $configRaw = json_decode(file_get_contents(\Ease\Shared::cfg('CONFIGS') . $candidat),
//                    true);
//            if (array_key_exists('ABRAFLEXI_URL', $configRaw)) {
//                $_SESSION['bookmarks'][pathinfo($candidat, PATHINFO_FILENAME)] = [
//                    'login' => $configRaw['ABRAFLEXI_LOGIN'], 'password' => $configRaw['ABRAFLEXI_PASSWORD'],
//                    'server' => $configRaw['ABRAFLEXI_URL'], 'company' => $configRaw['ABRAFLEXI_COMPANY']
//                ];
//            }
//        }
//    }
// }
//
//
// $bookmarks = new \Ease\Html\UlTag(null, ['class' => 'list-group']);
// foreach ($_SESSION['bookmarks'] as $bookmarkName => $bookmark) {
//    $bookmarks->addItemSmart(new \Ease\Html\ATag('login.php?login=' . urlencode($bookmark['login']) . '&password=' . urlencode($bookmark['password']) . '&server=' . urlencode($bookmark['server']),
//                    '<strong>' . $bookmarkName . '</strong> ' . $bookmark['login'] . '@' . parse_url($bookmark['server'],
//                            PHP_URL_HOST)), ['class' => 'list-group-item']);
// }
// //$infoBlock->addItem($bookmarks);
//
// $loginColumn = $loginRow->addItem(new \Ease\TWB5\Col(4));
//
// $submit = new \Ease\TWB5\SubmitButton(_('Sign in'), 'success');
//
// $loginPanel = new \Ease\TWB5\Panel(new \Ease\Html\ImgTag('images/flexplorer-logo.png',
//                'FlexPlorer', ['class' => 'img-responsive']), 'success', null, $submit);
// $loginPanel->addItem(new \Ease\TWB5\FormGroup(_('AbraFlexi'),
//                new \Ease\Html\InputTextTag('server',
//                        $server ? $server : \Ease\Shared::cfg('DEFAULT_ABRAFLEXI_URL') ),
//                \Ease\Shared::cfg('DEFAULT_ABRAFLEXI_URL'),
//                _('AbraFlexi server URL. ex.:') . ' <a href="?server=https://localhost:5434">https://localhost:5434</a>'));
// $loginPanel->addItem(new \Ease\TWB5\FormGroup(_('User name'),
//                new \Ease\Html\InputTextTag('login',
//                        $login ? $login : \Ease\Shared::cfg('DEFAULT_ABRAFLEXI_LOGIN')
//                ), \Ease\Shared::cfg('DEFAULT_ABRAFLEXI_LOGIN'), _('Login name')));
// $loginPanel->addItem(new \Ease\TWB5\FormGroup(_('Password'),
//                new \Ease\TWB5\Widgets\PasswordInput('password',
//                        $password ? $password : \Ease\Shared::cfg('DEFAULT_ABRAFLEXI_PASSWORD')),
//                \Ease\Shared::cfg('DEFAULT_ABRAFLEXI_PASSWORD'), _('User\'s password')));
// $loginPanel->addItem(new \Ease\TWB5\FormGroup(_('Remeber me'),
//                new \Ease\TWB5\Widgets\TWBSwitch('remember-me', true), null,
//                _('Add this to Login History')));
//
// $loginPanel->body->setTagCss(['margin' => '20px']);
//
// $loginColumn->addItem($loginPanel);
//
//
// $oPage->container->addItem(new \Ease\TWB5\Form(['name' => 'Login'], $loginRow));
// $login = $oPage->getRequestValue('login');
// $password = $oPage->getRequestValue('password');
// $server = $oPage->getRequestValue('server');
// $backurl = $oPage->getRequestValue('backurl');
//
// if (empty($login) === true) {
//    $forceID = $oPage->getRequestValue('force_id', 'int');
//    if (!is_null($forceID)) {
//        \Ease\Shared::user(new User($forceID));
//        $oUser->setSettingValue('admin', true);
//        $oUser->addStatusMessage(_('Signed in as: ') . $oUser->getUserLogin(),
//                'success');
//        \Ease\Shared::user()->loginSuccess();
//
//        if (!is_null($backurl)) {
//            $oPage->redirect($backurl);
//        } else {
//            $oPage->redirect('index.php');
//        }
//    } else {
//        $oPage->addStatusMessage(_('Please confirm your login credentials'));
//    }
// } else {
//    $oUser = \Ease\Shared::user(new User());
//    if ($oUser->tryToLogin($_REQUEST)) {
//        if ($oPage->getRequestValue('remember-me')) {
//            $_SESSION['bookmarks'][] = ['login' => $login, 'password' => $password,
//                'server' => $server];
//            $oPage->addStatusMessage(_('Server added to bookmarks'));
//        }
//        if (isset($_SESSION['backurl'])) {
//            $oPage->redirect($_SESSION['backurl']);
//            unset($_SESSION['backurl']);
//        } else {
//            $oPage->redirect('index.php');
//        }
//    }
// }
//
//
// $connectionOptions = [
//    'url' => $server ? $server : \Ease\Shared::cfg('DEFAULT_ABRAFLEXI_URL'),
//    'user' => $login ? $login : \Ease\Shared::cfg('DEFAULT_ABRAFLEXI_LOGIN'),
//    'password' => $password ? $password : \Ease\Shared::cfg('DEFAULT_ABRAFLEXI_PASSWORD')
// ];
//
// //$loginPanel = new \Ease\TWB5\Panel(new \Ease\Html\ImgTag('images/flexplorer-logo.png',
// //                'FlexPlorer', ['class' => 'img-responsive']), 'success', null, $submit);
//
//
// $oPage->addItem(new \Ease\TWB5\Container(new ui\LoginForm($connectionOptions)));
//
// $oPage->addItem(new ui\PageBottom());
//
// $oPage->draw();
//

$server = ui\WebPage::getRequestValue('server') ?? \Ease\Shared::cfg('ABRAFLEXI_URL');

$shared = Shared::singleton();
$login = \Ease\Document::getRequestValue('login');
$password = \Ease\Document::getRequestValue('password');

if ($login) {
    try {
        $oUser = Shared::user(null, 'Flexplorer\User');
    } catch (PDOException $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }

    if ($oUser->tryToLogin($_POST)) {
        $oPage->redirect('main.php');
        session_write_close();

        exit;
    }
}

// $oPage->addItem(new PageTop(_('Sign In')));
$loginFace = new DivTag(null, ['id' => 'LoginFace']);

$oPage->container->addItem($loginFace);
$loginRow = new Row();
$infoColumn = $loginRow->addItem(new Col(4));
$infoBlock = $infoColumn->addItem(new Card(new ImgTag('images/flexplorer-logo.png')));
$infoBlock->addItem(new DivTag(_('Welcome to FlexPlorer'), ['style' => 'text-align: center;']));
$loginColumn = $loginRow->addItem(new Col(4));
$submit = new SubmitButton(_('Sign in'), 'success', ['id' => 'signin']);
$loginPanel = new Panel(
    new ImgTag('images/flexplorer-logo.png', 'logo', ['width' => 20]),
    'inverse',
    null,
    $submit,
);
$loginPanel->addItem(new InputGroup(_('Username'), new InputTextTag('login', $login, null, ['class' => 'form-control']), '', _('the username you chose')));
$loginPanel->addItem(new InputGroup(_('Password'), new InputPasswordTag('password', $password)));
$loginPanel->addItem(new InputGroup(_('Server'), new InputTextTag('server', $server)));
$loginPanel->body->setTagCss(['margin' => '20px']);
$loginColumn->addItem($loginPanel);

$featureList = new \Ease\Html\UlTag(null, ['class' => 'list-group']);
$featureList->addItemSmart(
    _('display the contents of all the available records in all companies'),
    ['class' => 'list-group-item'],
);
$featureList->addItemSmart(
    _('show the structure of evidence'),
    ['class' => 'list-group-item'],
);
$featureList->addItemSmart(
    _('send direct requests to the server and display results'),
    ['class' => 'list-group-item'],
);
$featureList->addItemSmart(
    _('set up ChangesAPI and add WebHooks'),
    ['class' => 'list-group-item'],
);
$featureList->addItemSmart(
    _('Test WebHook script processing changes from AbraFlexi answers'),
    ['class' => 'list-group-item'],
);
$featureList->addItemSmart(
    _('Collectively establish and abolish the accounting period'),
    ['class' => 'list-group-item'],
);
$featureList->addItemSmart(
    _('Evidnece distinguish which are inaccessible because of the license'),
    ['class' => 'list-group-item'],
);
$featureList->addItemSmart(
    _('Shown next to json result of the request and page AbraFlexi'),
    ['class' => 'list-group-item'],
);
$featureList->addItemSmart(
    _('Edit External ID numbers'),
    ['class' => 'list-group-item'],
);
$featureList->addItemSmart(
    _('PDF Preview of edited record'),
    ['class' => 'list-group-item'],
);

$featuresPanel = new \Ease\TWB5\Panel(_('Features'), 'info');

\Ease\WebPage::addItemCustom($featureList, $featuresPanel);
$loginRow->addColumn(4, $featuresPanel);

$oPage->container->addItem(new Form([], $loginRow));

$oPage->addItem(new PageBottom());
$oPage->draw();
