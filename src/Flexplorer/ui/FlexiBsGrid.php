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
 * Description of FlexiBsGrid.
 *
 * @author vitex
 */
class FlexiBsGrid extends BsGrid
{
    /**
     * Zdroj dat.
     */
    public \Flexplorer\Flexplorer $dataSource = null;

    public function __construct($dataSource, $properties = null)
    {
        parent::__construct($dataSource->getEvidence(), $properties);
    }
}
