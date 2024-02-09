<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Flexplorer\ui;

/**
 * Description of Menu
 *
 * @author vitex
 */
class Menu extends \Ease\TWB5\Navbar
{
    public function __construct()
    {
        parent::__construct();
        $this->addMenulLink('main.php', 'Main');
        $this->addMenulLink('companies.php', 'Companies');
        $this->addMenulLink('changesapi.php', 'ChangesApi');
        $this->addMenulLink('logout.php', 'Logout');


//about.php
//abraflexidata.php
//backups.php
//buttons.php
//companies.php
//company.php
//copycompany.php
//createinsert.php
//datasaver.php
//datasource.php
//datatable.php
//deletecompany.php
//delete.php
//document.php
//editor.php
//evidence.php
//evidences.php
//fakechange.php
//getbuttonxml.php
//getinformat.php
//getpdf.php
//change.php
//changesapi.php
//changes.php
//index.php
//keepAlive.php
//labed.php
//lasturl.php
//listbylabel.php
//login.php
//main.php
//newcompany.php
//permissions.php
//query.php
//resetcompany.php
//restorecompany.php
//savecompany.php
//searcher.php
//search.php
//settings.php
//ucetniobdobi.php
//viewevidence.php
//webhook.php
    }

    public function addMenulLink($url, $caption, $properties = [])
    {
        $properties['class'] = 'nav-link';
        $properties['aria-current'] = "page";
        $this->addItem(self::navItem(new \Ease\Html\ATag($url, $caption, $properties)));
    }

    public static function navItem($content)
    {
        return new \Ease\Html\LiTag($content, ['class' => 'nav-item']);
    }
}
