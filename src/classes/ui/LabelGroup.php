<?php
/**
 * Flexplorer - labelgroup
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
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
     * @param \FlexiPeeHP\FlexiBeeRO $flexibee
     */
    public function __construct($flexibee)
    {
        $labels = [];
        $stitky = $flexibee->getDataValue('stitky');
        parent::__construct();
        if (count($stitky)) {
            if (is_array($stitky)) {
                $stitkyArr = $stitky;
            } else {
                $stitkyArr = explode(',', $stitky);
            }
            foreach ($stitkyArr as $code => $stitek) {
                $this->addItem(new \Ease\TWB\Label('info',
                    new \Ease\Html\ATag('listbylabel.php?label='.trim($stitek).'&evidence='.$flexibee->getEvidence(),
                    is_numeric($code) ? trim($stitek) : new \Ease\TWB\Label('info',
                        $code, ['title' => trim($stitek)]))));
            }
        }
    }
}
