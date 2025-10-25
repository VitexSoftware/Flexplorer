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
 * Description of BooleanLabel.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class BooleanLabel extends \Ease\TWB5\Badge
{
    /**
     * Show boolean Label.
     *
     * @param bool                  $bool       state
     * @param array<string, string> $properties options
     */
    public function __construct($bool, $properties = [])
    {
        parent::__construct(
            $bool ? _('Yes') : _('No'),
            $bool ? 'success' : 'warning',
            $properties,
        );
    }
}
