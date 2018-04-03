<?php

namespace Flexplorer;

/**
 * Flexplorer - Nastavení uživatele stránka.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$od = $oPage->getRequestValue('od');
$do = $oPage->getRequestValue('do');

$uo = new \FlexiPeeHP\UcetniObdobi();

/**
 * Create requested Accounting period
 *
 * @param int $startYear first year to create
 * @param int $endYear   last yar to create - default is current year
 *
 * @return array Results
 */
function createYearsFrom($uo, $startYear, $endYear = null)
{
    $result = [];
    if (is_null($endYear)) {
        $endYear = date('Y');
    }

    for ($year = $startYear; $year <= $endYear; ++$year) {
        $obdobi = ['kod' => $year,
            'platiOdData' => $year.'-01-01T00:00:00',
            'platiDoData' => $year.'-12-31T23:59:59',
        ];
        if ($uo->idExists('code:'.$year)) {
            $uo->addStatusMessage(sprintf(_('%s already exists.'), $year));
        } else {
            $uo->setData($obdobi);
            $result[] = $uo->insertToFlexibee();
            $uo->dataReset();
        }
    }
    return $result;
}
if (!is_null($od)) { 
   createYearsFrom($uo, $od, $do);
}

$yeardel = $oPage->getRequestValue('yeardel', 'int');
if (!is_null($yeardel)) {
    if ($yeardel === 0) {
        foreach ($uo->getFlexiData() as $obdobi) {
            if ($uo->deleteFromFlexiBee((int) $obdobi['id'])) {
                $uo->addStatusMessage(sprintf(_('Year %s was unregistred'),
                        $obdobi['kod']), 'success');
            } else {
                $uo->addStatusMessage(sprintf(_('Year %s was not unregistred'),
                        $obdobi['kod']), 'warning');
            }
        }
    } else {
        if ($uo->deleteFromFlexiBee($yeardel)) {
            $uo->addStatusMessage(_('Year was unregistred'), 'success');
        } else {
            $uo->addStatusMessage(_('Year was not unregistred'), 'warning');
        }
    }
}

$oPage->addItem(new ui\PageTop(_('Accounting period')));

$toolRow      = new \Ease\TWB\Row();
$settingsForm = new \Ease\TWB\Form('settings');

$settingsForm->addInput(new \Ease\Html\InputNumberTag('od', null,
        ['min' => 1980]), _('From Year'), date('Y') - 2);

$settingsForm->addInput(new \Ease\Html\InputNumberTag('od', date('Y'),
        ['min' => 1980]), _('To Year'), date('Y') + 2);

$settingsForm->addItem(new \Ease\TWB\SubmitButton(_('Perform operation'),
        'warning'));
$toolRow->addColumn(6, new \Ease\TWB\Well($settingsForm));


$ucetniObdobi = $uo->getFlexiData();
if (!isset($ucetniObdobi['message']) && count($ucetniObdobi)) {
    $ucetniObdobiTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
    $ucetniObdobiTable->addRowHeaderColumns(array_keys(current($ucetniObdobi)));
    foreach ($ucetniObdobi as $hookinfo) {
        $hookinfo[] = new \Ease\TWB\LinkButton('?yeardel='.$hookinfo['id'],
            new \Ease\TWB\GlyphIcon('remove'), 'warning');
        $ucetniObdobiTable->addRowColumns($hookinfo);
    }

    $toolRow->addColumn(6,
        new \Ease\TWB\Panel(_('Registered Accounting periods'), 'info',
            $ucetniObdobiTable,
            new \Ease\TWB\LinkButton('?yeardel=0', _('Remove unused'), 'warning')));
}

$oPage->container->addItem(new \Ease\TWB\Panel(_('Tool for massive creating Accounting periods'),
        'info', $toolRow));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
