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
class PageTop extends \Ease\Html\DivTag {

    /**
     * Titulek stránky.
     *
     * @var string
     */
    public $pageTitle = 'Page Heading';

    /**
     * Nastavuje titulek.
     *
     * @param string $pageTitle
     */
    public function __construct($pageTitle = null) {
        parent::__construct();
        if (!is_null($pageTitle)) {
            WebPage::singleton()->setPageTitle($pageTitle);
        }
    }

    /**
     * Vloží vršek stránky a hlavní menu.
     */
    public function finalize() {
        if ($this->finalized != true) {
            $this->addItem(new MainMenu());
            $this->addItem(new \Ease\TWB\Widgets\BrowsingHistory());
            $this->finalized = true;
        }
    }

}
