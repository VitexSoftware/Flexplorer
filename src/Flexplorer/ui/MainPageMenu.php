<?php

/**
 * Flexplorer - Menu hlavní stránky.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class MainPageMenu extends \Ease\Html\Div
{
    /**
     * Sem se přidávají položky.
     *
     * @var \Ease\Html\DivTag
     */
    public $row = null;

    /**
     * Rámeček nabídky.
     *
     * @var \Ease\Html\DivTag
     */
    public $well = null;

    public function __construct()
    {
        parent::__construct(
            null,
            null,
            [
                    'class' => 'container', 'style' => 'margin: auto;',
                ]
        );
        $this->well = $this->addItem(
            new \Ease\Html\DivTag(null, ['class' => 'well'])
        );
        $this->row = $this->well->addItem(
            new \Ease\Html\DivTag(null, ['class' => 'row'])
        );
    }

    public function addMenuItem($image, $title, $url)
    {
        return $this->row->addItem(
            new \Ease\Html\ATag(
                $url,
                new \Ease\Html\DivTag(
                    "$title<center><img class=\"mpicon\" src=\"$image\" alt=\"$title\"></center>",
                    ['class' => 'col-md-2']
                )
            )
        );
    }

    public function finalize()
    {
    }
}
