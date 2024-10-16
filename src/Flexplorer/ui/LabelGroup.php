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
 * Description of LabelGroup.
 *
 * @author vitex
 */
class LabelGroup extends \Ease\Container
{
    /**
     * Address.
     *
     * @param \AbraFlexi\RO $abraflexi
     */
    public function __construct($abraflexi)
    {
        $labels = [];
        $stitky = $abraflexi->getDataValue('stitky');
        parent::__construct();

        if (\count($stitky)) {
            if (\is_array($stitky)) {
                $stitkyArr = $stitky;
            } else {
                $stitkyArr = explode(',', $stitky);
            }

            foreach ($stitkyArr as $code => $stitek) {
                $this->addItem(new \Ease\TWB5\Badge(
                    'info',
                    new \Ease\Html\ATag(
                        'listbylabel.php?label='.trim($stitek).'&evidence='.$abraflexi->getEvidence(),
                        is_numeric($code) ? trim($stitek) : new \Ease\TWB5\Badge(
                            'info',
                            $code,
                            ['title' => trim($stitek)],
                        ),
                    ),
                ));
                $this->addItem(' ');
            }
        }
    }
}
