<?php

/**
 * Flexplorer - menu.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2024 Vitex Software
 */

namespace Flexplorer\ui;

class BootstrapMenu extends \Ease\TWB5\Navbar
{
    /**
     * Navigace.
     *
     * @var \Ease\Html\UlTag
     */
    public $nav = null;

    /**
     *
     * @var
     */
    public $container;

    /**
     * Hlavní menu aplikace.
     *
     * @param string $name
     * @param mixed  $content
     * @param array  $properties
     */
    public function __construct(
        $name = null,
        $content = null,
        $properties = null
    ) {
        parent::__construct('', '', ['class' => 'navbar']);

        $this->container = $this->addItem(new \Ease\Html\DivTag(new \Ease\Html\ATag('index.php', new \Ease\Html\ImgTag('images/flexplorer-logo.png', 'Flexplorer', ['class' => 'img-rounded', 'width' => 24]), ['class' => 'navbar-brand']), ['class' => 'container-fluid']));

        $this->container->addItem(new \Ease\Html\ButtonTag(new \Ease\Html\SpanTag('', ['class' => 'navbar-toggler-icon']), ['class' => 'navbar-toggler', 'type' => 'button', 'data-bs-toggle' => 'collapse', 'data-bs-target' => '#navbarSupportedContent', 'aria-controls' => 'navbarSupportedContent', 'aria-expanded' => false, 'aria-label' => _('Toggle navigation')]));

//        $user = \Ease\Shared::user();
//        \Ease\TWB5\Part::twBootstrapize();
//        if (!$user->getUserID()) {
//            if (get_class($user) != 'EaseAnonym') {
//                $this->addDropDownMenu(
//                        _('Tools'),
//                        [
//                            'permissions.php' => _('Role Permissions')
//                        ]
//                );
//
//                $this->addMenuItem(
//                        new \Ease\Html\ATag('about.php', _('About')),
//                        'right'
//                );
//
//                $this->addMenuItem(
//                        '
//<li class="divider-vertical"></li>
//<li class="dropdown">
//<a class="dropdown-toggle" href="login.php" data-toggle="dropdown"><i class="icon-circle-arrow-left"></i> ' . _('Sign in') . '<strong class="caret"></strong></a>
//<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px; left: -160px;">
//<form method="post" class="navbar-form navbar-left" action="login.php" accept-charset="UTF-8">
//<input style="margin-bottom: 15px;" type="text" placeholder="' . _('Server') . '" id="server" name="server">
//<input style="margin-bottom: 15px;" type="text" placeholder="' . _('login') . '" id="username" name="login">
//<input style="margin-bottom: 15px;" type="password" placeholder="' . _('Password') . '" id="password" name="password">
//<input style="float: left; margin-right: 10px;" type="checkbox" name="remember-me" id="remember-me" value="1">
//<label class="string optional" for="remember-me"> ' . _('Remeber me') . '</label>
//<input class="btn btn-primary btn-block" type="submit" id="sign-in" value="' . _('Sign in') . '">
//</form>
//</div>',
//                        'right'
//                );
//            }
//        } else {
//            $userMenu = '<li class="dropdown" style="width: 120px; text-align: right;"><a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $_SESSION['user'] . ' <b class="caret"></b></a>
//<ul class="dropdown-menu" style="text-align: left; left: -60px;">
//<li><a href="settings.php">🛠️ ' . _('Settings') . '</a></li>
//';
//
//            $this->addMenuItem($userMenu . '
//<li><a href="about.php">🤬 ' . _('About') . '</a></li>
//<li class="divider"></li>
//<li><a href="logout.php">🚪 ' . _('Sign off') . '</a></li>
//</ul>
//</li>
//', 'right');
//        }
    }
}
