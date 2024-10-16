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
 * Description of WebHookSelect.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class WebHookSelect extends \Ease\Html\Select
{
    public function __construct(
        $name,
        $items = null,
        $defaultValue = null,
        $properties = []
    ) {
        $items = [];
        $hooker = new \AbraFlexi\Hooks();
        $hooks = $hooker->getFlexiData();

        if (!isset($hooks['message']) && \count($hooks) && \count(current($hooks))) {
            foreach ($hooks as $hook) {
                $items[$hook['url']] = $hook['dataFormat'].' '.$hook['url'];
            }
        }

        parent::__construct($name, $items, $defaultValue, null, $properties);
    }
}
