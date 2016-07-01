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
        $uo->addStatusMessage(_('Rok byl odregistrován'), 'success');
    } else {
        $uo->addStatusMessage(_('Rok nebyl odregistrován'), 'warning');
    }
}



$oPage->addItem(new ui\PageTop(_('Účetní období')));

$toolRow      = new \Ease\TWB\Row();
$settingsForm = new \Ease\TWB\Form('settings');

$settingsForm->addInput(new \Ease\Html\InputNumberTag('od', null,
    ['min' => 1980]), _('Od Roku'), date('Y') - 2);

$settingsForm->addInput(new \Ease\Html\InputNumberTag('od', date('Y'),
    ['min' => 1980]), _('Do Roku'), date('Y') + 2);



$settingsForm->addItem(new \Ease\TWB\SubmitButton(_('Provést operaci'),
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
        new \Ease\TWB\Panel(_('Zaregistrované ucetniObdobi'), 'info',
        $ucetniObdobiTable));
}

$oPage->container->addItem(new \Ease\TWB\Panel(_('Nástroj pro hromadné zakládání účetních období'),
    'info', $toolRow));


$oPage->addItem(new ui\PageBottom());

$oPage->draw();
