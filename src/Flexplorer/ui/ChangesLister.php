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

use Ease\Html\Widgets\LiveAge;

/**
 * Description of ChangesLister.
 *
 * @author vitex
 */
class ChangesLister extends \Ease\Html\UlTag
{
    /**
     * Summary of addItemSmart.
     *
     * @param mixed                 $pageItem
     * @param array<string, string> $properties
     */
    public function &addItemSmart($pageItem, $properties = [])
    {
        [$tmp, $stamp] = explode('_', $pageItem);
        $age = new LiveAge((new \DateTime())->setTimestamp((int) str_replace('.json', '', $stamp)));
        $pageItem = new \Ease\Html\ATag(
            'change.php?file='.$pageItem,
            str_replace(
                ['flexplorer-changes-', '_'],
                ['', '&nbsp;<strong>'],
                $pageItem,
            ).'</strong> '.$age,
        );
        $item = parent::addItemSmart($pageItem, $properties);

        return $item;
    }
}
