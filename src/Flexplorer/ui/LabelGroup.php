<?php

/**
 * Flexplorer - labelgroup
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of LabelGroup
 *
 * @author vitex
 */
class LabelGroup extends \Ease\Container
{
    /**
     * Address
     * @param \AbraFlexi\RO $abraflexi
     */
    public function __construct($abraflexi)
    {
        $labels = [];
        $stitky = $abraflexi->getDataValue('stitky');
        parent::__construct();
        if (count($stitky)) {
            if (is_array($stitky)) {
                $stitkyArr = $stitky;
            } else {
                $stitkyArr = explode(',', $stitky);
            }
            foreach ($stitkyArr as $code => $stitek) {
                $this->addItem(new \Ease\TWB5\Label(
                    'info',
                    new \Ease\Html\ATag(
                        'listbylabel.php?label=' . trim($stitek) . '&evidence=' . $abraflexi->getEvidence(),
                        is_numeric($code) ? trim($stitek) : new \Ease\TWB5\Label(
                            'info',
                            $code,
                            ['title' => trim($stitek)]
                        )
                    )
                ));
                $this->addItem(' ');
            }
        }
    }
}
