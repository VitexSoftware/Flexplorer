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

namespace Flexplorer\xml;

/**
 * Description of CustomButton.
 *
 * @author vitex
 */
class LocationTag extends \Ease\Html\PairTag
{
    public function __construct($location)
    {
        parent::__construct('location', null, $location);
    }
}
