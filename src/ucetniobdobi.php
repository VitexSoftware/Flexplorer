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


if (!is_null($od)) {

    $uo->createYearsFrom($od, $do);
}

$yeardel = $oPage->getRequestValue('yeardel', 'int');
if (!is_null($yeardel)) {
    if ($uo->deleteFromFlexiBee($yeardel)) {
        $uo->addStatusMessage(_('Year was unregistred'), 'success');
    } else {
        $uo->addStatusMessage(_('Yeas was not unregistred'), 'warning');
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
        $ucetniObdobiTable));
}

$oPage->container->addItem(new \Ease\TWB\Panel(_('Tool for massive creating Accounting periods'),
    'info', $toolRow));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
