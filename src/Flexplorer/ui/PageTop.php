<?php

/**
 * Flexplorer - vršek stránky.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Page TOP.
 */
class PageTop extends \Ease\Html\DivTag
{
    /**
     * Titulek stránky.
     *
     * @var string
     */
    public $pageTitle = null;

    /**
     * Nastavuje titulek.
     *
     * @param string $pageTitle
     */
    public function __construct($pageTitle = null)
    {
        parent::__construct();
        if (!is_null($pageTitle)) {
            WebPage::singleton()->setPageTitle($pageTitle);
        }
        WebPage::singleton()->body->addAsFirst(new MainMenu());
    }

    /**
     * Vloží vršek stránky a hlavní menu.
     */
    public function finalize()
    {
        if ($this->finalized != true) {
            $this->addItem(new MainMenu());
//            $this->addItem(new \Ease\TWB5\Widgets\BrowsingHistory());
            $this->finalized = true;
        }
    }
}
