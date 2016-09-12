<?php

namespace Flexplorer;

/**
 * Flexplorer - Changes API.
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
            $changer->addStatusMessage(_('ChangesAPI was enabled'), 'success');
            $chapistatus = true;
        }
    } else {
        if ($chapistatus === TRUE) {
            $changer->disable();
            $changer->addStatusMessage(_('ChangesAPI was disabled'), 'warning');
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
        if (!is_null($lastversion) && $lastversion) {
            $hooker->setDataValue('lastVersion', $lastversion);
        }

        $secKey = $oPage->getRequestValue('secKey');
        if (strlen($secKey)) {
            $hooker->setDataValue('secKey', $secKey);
        }

        $hookResult = $hooker->register($hookurl, $format);
        if ($hookResult) {
            $hooker->addStatusMessage(sprintf(_('Hook %s was registered'),
                    $hookurl), 'success');
        } else {
            $hooker->addStatusMessage(sprintf(_('Hook %s not registered'),
                    $hookurl), 'warning');
        }
    }
}

$linkdel = $oPage->getRequestValue('linkdel', 'int');
if (!is_null($linkdel)) {
    if ($hooker->unregister($linkdel)) {
        $hooker->addStatusMessage(_('Hook was unregistered'), 'success');
    } else {
        $hooker->addStatusMessage(_('Hook was not unregistered'), 'warning');
    }
    $oPage->redirect('changesapi.php');
}

$linkrefresh = $oPage->getRequestValue('refresh', 'int');
if (!is_null($linkrefresh)) {
    if ($hooker->refresh($linkrefresh)) {
        $hooker->addStatusMessage(_('Hook refreshed'), 'success');
    } else {
        $hooker->addStatusMessage(_('Hook refresh failed'), 'warning');
    }
    $oPage->redirect('changesapi.php');
}

$oPage->addItem(new ui\PageTop(_('ChangesAPI Tool')));
$toolRow      = new \Ease\TWB\Row();
$settingsForm = new \Ease\TWB\Form('settings');
$settingsForm->addInput(new ui\TWBSwitch('changesapi', $chapistatus, 'enable',
    ['onText' => _('Enable'), 'offText' => _('Disable')]), _('Changes API'),
    null,
    new \Ease\Html\ATag('https://www.flexibee.eu/api/dokumentace/ref/changes-api/',
    _('Je-li to zapnuto, FlexiBee zaznamenává všechny změny provedené v databázi firmy do changelogu a umožňuje seznam změn zpětně získat')));

$settingsForm->addInput(new \Ease\Html\InputTextTag('hookurl'), _('Web Hook'),
    'http://server/getchanges.php',
    new \Ease\Html\ATag('https://www.flexibee.eu/api/dokumentace/ref/web-hooks',
    _('Když dojde v databázi FlexiBee ke změně, je odeslán POST HTTP request na všechna zaregistrovaná URL'))
);

$settingsForm->addInput(new ui\TWBSwitch('changesformat', true, 'JSON',
    ['onText' => 'JSON', 'offText' => 'XML']), _('Data format'));

$settingsForm->addInput(new ui\TWBSwitch('hookurltest', true, 'skip'),
    _('Přeskočit test URL'), null, _('Suppress URL functionality test'));


$settingsForm->addInput(new \Ease\Html\InputNumberTag('lastVersion', null,
    ['min' => 0, 'max' => $globalVersion]), _('Last version'), $globalVersion,
    sprintf(_('Verze od které započne posílání následujích změn, tj. od nejbližší vyšší verze. Defaultní hodnota je rovna aktuální globální verzi (globalVersion) v momentě registrace hooku. Přípustné hodnoty jsou z intervalu: [0, %s]'),
        $globalVersion));

$randstr = \Ease\Sand::randomString(30);
$settingsForm->addInput(new \Ease\Html\InputTextTag('secKey'),
    _('Bezpečnostní kód'), $randstr,
    sprintf(_('Libovolný řetězec, (např. %s) který bude odesílán s každou notifikací změn v HTTP hlavičce. Slouží k jednoduchému ověření, zda patří příchozí notifikace Vámi registrovanému hooku. Název klíče v HTTP hlavičce je X-FB-Hook-SecKey.'),
        $randstr)
);


$settingsForm->addItem(new \Ease\TWB\SubmitButton(_('Perform operation'),
    'warning'));
$toolRow->addColumn(4, new \Ease\TWB\Well($settingsForm));

if ($chapistatus) {
    $hooks = $hooker->getFlexiData();
    if (!isset($hooks['message']) && count($hooks)) {
        $hooksTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
        $hooksTable->addRowHeaderColumns(array_keys(current($hooks)));
        foreach ($hooks as $hookinfo) {
            $hookinfo[] = new \Ease\TWB\LinkButton('?refresh='.$hookinfo['id'],
                new \Ease\TWB\GlyphIcon('refresh'), 'success');
            $hookinfo[] = new \Ease\TWB\LinkButton('fakechange.php?hookurl='.$hookinfo['url'],
                new \Ease\TWB\GlyphIcon('export'), 'info',
                ['title' => _('Test')]);
            $hookinfo[] = new \Ease\TWB\LinkButton('?linkdel='.$hookinfo['id'],
                new \Ease\TWB\GlyphIcon('remove'), 'danger');
            $hooksTable->addRowColumns($hookinfo);
        }

        $toolRow->addColumn(8,
            new \Ease\TWB\Panel(_('Webhooks registered'), 'info', $hooksTable));
    }
}
$oPage->container->addItem(new \Ease\TWB\Panel(_('ChangesAPI & WebHooks'),
    'info', $toolRow));




$oPage->addItem(new ui\PageBottom());

$oPage->draw();
