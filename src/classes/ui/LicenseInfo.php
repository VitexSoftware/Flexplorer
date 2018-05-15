<?php
/**
 * Flexplorer - Show FlexiBee license properties.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of LicenseInfo
 *
 * @author vitex
 */
class LicenseInfo extends \Ease\Html\TableTag
{

    public function __construct($licenseInfo)
    {
        parent::__construct(null, ['class' => 'table']);

        $licenseInfo['key'] = new \Ease\Html\ATag('https://www.flexibee.eu/moje-licence/?key='.$licenseInfo['key'],
            $licenseInfo['key'], ['target' => '_blank']);

        $licenseInfo['legislations'] = implode(',',
            $licenseInfo['legislations']['legislation']);
        $licenseInfo['languages']    = implode(',',
            $licenseInfo['languages']['lang']);
        $licenseInfo['modules']      = implode(',',
            $licenseInfo['modules']['module']);


        foreach ($licenseInfo as $licenseKey => $licenseValue) {
            switch ($licenseKey) {
                case 'modules':
                    $licenseValue = new ModulesOverview(\FlexiPeeHP\Stitek::listToArray($licenseValue));
                    break;
                case 'features':
                    $features = [];
                    foreach ($licenseValue['feature'] as $feature) {
                        $features[] = new \Ease\TWB\Label('success', $feature);
                        $features[] = ' ';
                    }
                    $licenseValue = $features;
                    break;
                default:
                    break;
            }
            $this->addRowColumns([$licenseKey, $licenseValue]);
        }
    }
}
