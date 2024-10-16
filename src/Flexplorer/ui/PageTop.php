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
        parent::finalize();
    }
}
