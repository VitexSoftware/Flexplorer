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

namespace Flexplorer;

/**
 * Flexplorer - Data source.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$evidence = $oPage->getRequestValue('evidence');

if (\strlen($evidence)) {
    $datasource = new DataSource(new Flexplorer($evidence));
    $datasource->output();
} else {
    $stitek = $oPage->getRequestValue('stitek');

    if (\strlen($stitek)) {
        $datasource = new DataSource(new SearchFlexplorer(['stitek' => $stitek]));

        $datasource->output();
    }
}
