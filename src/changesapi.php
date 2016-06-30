<?php

namespace Flexplorer;

/**
 * Flexplorer - Odhlašovací stránka.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();


$changer       = new \FlexiPeeHP\Changes();
$hooker        = new \FlexiPeeHP\Hooks();
$chapistatus   = $changer->getStatus();
$invoicer      = new \FlexiPeeHP\FakturaVydana();
$globalVersion = $invoicer->getGlobalVersion();

if ($oPage->isPosted()) {
    if ($oPage->getRequestValue('changesapi') === 'enable') {
        if ($chapistatus === FALSE) {
            $changer->enable();
            $changer->addStatusMessage(_('ChangesAPI bylo povoleno'), 'success');
            $chapistatus = true;
        }
    } else {
        if ($chapistatus === TRUE) {
            $changer->disable();
            $changer->addStatusMessage(_('ChangesAPI bylo zakázáno'), 'warning');
            $chapistatus = false;
        }
    }

    $hookurl = $oPage->getRequestValue('hookurl');
    if (strlen($hookurl)) {

        if ($oPage->getRequestValue('changesformat') === 'JSON') {
            $format = 'json';
        } else {
            $format = 'xml';
        }
        if ($oPage->getRequestValue('hookurltest') === 'skip') {
            $hooker->setDataValue('skipUrlTest', 'true');
        } else {
            $hooker->setDataValue('skipUrlTest', 'false');
        }
        $lastversion = $oPage->getRequestValue('lastVersion', 'int');
        if (!is_null($lastversion)) {
            $hooker->setDataValue('lastVersion', $lastversion);
        }

        $secKey = $oPage->getRequestValue('secKey');
        if (strlen($secKey)) {
            $hooker->setDataValue('secKey', $secKey);
        }

        $hookResult = $hooker->register($hookurl, $format);
        if ($hookResult) {
            $hooker->addStatusMessage(sprintf(_('Hook %s byl zaregistrován'),
                    $hookurl), 'success');
        } else {
            $hooker->addStatusMessage(sprintf(_('Hook %s nebyl zaregistrován'),
                    $hookurl), 'warning');
        }
    }
}

$linkdel = $oPage->getRequestValue('linkdel', 'int');
if (!is_null($linkdel)) {
    if ($hooker->unregister($linkdel)) {
        $hooker->addStatusMessage(_('Hook byl odregistrován'), 'success');
    } else {
        $hooker->addStatusMessage(_('Hook nebyl odregistrován'), 'warning');
    }
}


$oPage->addItem(new ui\PageTop(_('Nastavení rozhraní sledování změn')));
$toolRow      = new \Ease\TWB\Row();
$settingsForm = new \Ease\TWB\Form('settings');
$settingsForm->addInput(new ui\TWBSwitch('changesapi', $chapistatus, 'enable',
    ['onText' => _('Zapnuto'), 'offText' => _('Vypnuto')]), _('Changes API'),
    null,
    new \Ease\Html\ATag('https://www.flexibee.eu/api/dokumentace/ref/changes-api/',
    _('Je-li to zapnuto, FlexiBee zaznamenává všechny změny provedené v databázi firmy do changelogu a umožňuje seznam změn zpětně získat')));

$settingsForm->addInput(new \Ease\Html\InputTextTag('hookurl'), _('Web Hook'),
    'http://server/getchanges.php',
    new \Ease\Html\ATag('https://www.flexibee.eu/api/dokumentace/ref/web-hooks',
    _('Když dojde v databázi FlexiBee ke změně, je odeslán POST HTTP request na všechna zaregistrovaná URL'))
);

$settingsForm->addInput(new ui\TWBSwitch('changesformat', true, 'JSON',
    ['onText' => 'JSON', 'offText' => 'XML']), _('Formát dat'));

$settingsForm->addInput(new ui\TWBSwitch('hookurltest', true, 'skip'),
    _('Přeskočit test URL'), null, _('Potlačení testu funkčnosti předaného URL'));


    $settingsForm->addInput(new \Ease\Html\InputNumberTag('lastVersion', null,
        ['min' => 0, 'max' => $globalVersion]), _('Poslední verze'),
        $globalVersion,
        sprintf(_('Verze od které započne posílání následujích změn, tj. od nejbližší vyšší verze. Defaultní hodnota je rovna aktuální globální verzi (globalVersion) v momentě registrace hooku. Přípustné hodnoty jsou z intervalu: [0, %s]'),
            $globalVersion));

$randstr = \Ease\Sand::randomString(30);
$settingsForm->addInput(new \Ease\Html\InputTextTag('secKey'),
    _('Bezpečnostní kód'), $randstr,
    sprintf(_('Libovolný řetězec, (např. %s) který bude odesílán s každou notifikací změn v HTTP hlavičce. Slouží k jednoduchému ověření, zda patří příchozí notifikace Vámi registrovanému hooku. Název klíče v HTTP hlavičce je X-FB-Hook-SecKey.'),
        $randstr)
);


$settingsForm->addItem(new \Ease\TWB\SubmitButton(_('Provést operaci'),
    'warning'));
$toolRow->addColumn(6, new \Ease\TWB\Well($settingsForm));

$hooks = $hooker->getFlexiData();
if (!isset($hooks['message']) && count($hooks)) {
    $hooksTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
    $hooksTable->addRowHeaderColumns(array_keys(current($hooks)));
    foreach ($hooks as $hookinfo) {
        $hookinfo[] = new \Ease\TWB\LinkButton('?linkdel='.$hookinfo['id'],
            new \Ease\TWB\GlyphIcon('remove'), 'warning');
        $hooksTable->addRowColumns($hookinfo);
    }

    $toolRow->addColumn(6,
        new \Ease\TWB\Panel(_('Zaregistrované webhooks'), 'info', $hooksTable));
}

$oPage->container->addItem(new \Ease\TWB\Panel(_('ChangesAPI a WebHooks'),
    'info', $toolRow));




$oPage->addItem(new ui\PageBottom());

$oPage->draw();
