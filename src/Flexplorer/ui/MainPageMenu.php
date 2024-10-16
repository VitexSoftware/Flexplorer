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

class MainPageMenu extends \Ease\Html\Div
{
    /**
     * Sem se přidávají položky.
     */
    public \Ease\Html\DivTag $row = null;

    /**
     * Rámeček nabídky.
     */
    public \Ease\Html\DivTag $well = null;

    public function __construct()
    {
        parent::__construct(
            null,
            null,
            [
                'class' => 'container', 'style' => 'margin: auto;',
            ],
        );
        $this->well = $this->addItem(
            new \Ease\Html\DivTag(null, ['class' => 'well']),
        );
        $this->row = $this->well->addItem(
            new \Ease\Html\DivTag(null, ['class' => 'row']),
        );
    }

    public function addMenuItem($image, $title, $url)
    {
        return $this->row->addItem(
            new \Ease\Html\ATag(
                $url,
                new \Ease\Html\DivTag(
                    "{$title}<center><img class=\"mpicon\" src=\"{$image}\" alt=\"{$title}\"></center>",
                    ['class' => 'col-md-2'],
                ),
            ),
        );
    }

    public function finalize(): void
    {
    }
}
