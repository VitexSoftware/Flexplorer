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

/**
 * Page TOP.
 */
class PageTop extends \Ease\Html\DivTag
{
    /**
     * Titulek stránky.
     */
    public string $pageTitle = '';

    /**
     * Nastavuje titulek.
     *
     * @param string $pageTitle
     */
    public function __construct($pageTitle = '')
    {
        parent::__construct();

        if ($pageTitle) {
            \Ease\WebPage::singleton()->setPageTitle($pageTitle);
        }

        $this->setTagID('header');
    }

    /**
     * Vloží vršek stránky a hlavní menu.
     */
    public function finalize(): void
    {
        $this->addItem(new MainMenu('menu', new \Ease\Html\ATag('index.php', new \Ease\Html\ImgTag('images/flexplorer-logo.png', 'Flexplorer', ['width' => 25]))));

        //        if (\Ease\Shared::user()->isLogged()) { //Authenticated user
        // //            $this->addItem(new Breadcrumb());        }
        if (!empty(\Ease\Shared::logger()->getMessages())) {
            WebPage::singleton()->addCss(<<<'EOD'

         #smdrag { height: 8px;
                  background-image:  url( images/slidehandle.png );
                  background-color: #ccc;
                  background-repeat: no-repeat;
                  background-position: top center;
                  cursor: ns-resize;
         }
         #smdrag:hover { background-color: #f5ad66; }


EOD);
            $this->addItem(new \Ease\Html\DivTag('<br>'));
            $this->addItem(WebPage::singleton()->getStatusMessagesBlock(['id' => 'status-messages', 'title' => _('Click to hide messages')]));
            $this->addItem(new \Ease\Html\DivTag(null, ['id' => 'smdrag', 'style' => 'margin-bottom: 5px']));
            \Ease\Shared::logger()->cleanMessages();
            WebPage::singleton()->addCss('.dropdown-menu { overflow-y: auto } ');
            WebPage::singleton()->addJavaScript(
                "$('.dropdown-menu').css('max-height',$(window).height()-100);",
                null,
                true,
            );
            WebPage::singleton()->includeJavaScript('js/slideupmessages.js');
            //           WebPage::singleton()->includeJavaScript('https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js');
        }

        //        }

        parent::finalize();
    }
}
