<?php

namespace Flexplorer;

/**
 * Flexplorer - Changes API.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016-2024 Vitex Software
 */

require_once 'includes/Init.php';

$oPage->onlyForLogged();

$changer = new \AbraFlexi\Changes();
$hooker = new \AbraFlexi\Hooks();
$chapistatus = $changer->getStatus();
$invoicer = new \AbraFlexi\FakturaVydana();
try {
    $globalVersion = $changer->getGlobalVersion();
} catch (\AbraFlexi\Exception $exc) {
    $globalVersion = null;
}

$hookurl = $oPage->getRequestValue('hookurl');

if ($oPage->isPosted()) {
    if ($oPage->getRequestValue('changesapi') === 'enable') {
        if ($chapistatus === false) {
            $changer->enable();
            $changer->addStatusMessage(_('ChangesAPI was enabled'), 'success');
            $chapistatus = true;
        }
    } else {
        if ($chapistatus === true) {
            $changer->disable();
            $changer->addStatusMessage(_('ChangesAPI was disabled'), 'warning');
            $chapistatus = false;
        }
    }

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
            $hooker->addStatusMessage(sprintf(
                _('Hook %s was registered'),
                $hookurl
            ), 'success');
            $hookurl = '';
        } else {
            $hooker->addStatusMessage(sprintf(
                _('Hook %s not registered'),
                $hookurl
            ), 'warning');
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
    if ($hooker->refreshWebHook($linkrefresh)) {
        $hooker->addStatusMessage(_('Hook refreshed'), 'success');
    } else {
        $hooker->addStatusMessage(_('Hook refresh failed'), 'warning');
    }
    $oPage->redirect('changesapi.php');
}

$oPage->addItem(new ui\PageTop(_('ChangesAPI Tool')));
$toolRow = new \Ease\TWB5\Row();
$settingsForm = new \Ease\TWB5\Form(['name' => 'settings']);
$settingsForm->addInput(
    new \Ease\Html\Widgets\Toggle(
        'changesapi',
        $chapistatus,
        ['onText' => _('Enable'), 'offText' => _('Disable')]
    ),
    _('Changes API'),
    null,
    new \Ease\Html\ATag(
        'https://www.flexibee.eu/api/dokumentace/ref/changes-api/',
        _('If it is turned on , AbraFlexi records all changes made to the database company in the changelog and provides a list of changes recovered')
    )
);

$webHookUrl = str_replace(
    basename(__FILE__),
    'webhook.php',
    \Ease\Document::phpSelf()
);

$settingsForm->addInput(
    new \Ease\Html\InputTextTag('hookurl', $hookurl),
    _('Web Hook'),
    $webHookUrl,
    new \Ease\Html\ATag(
        'https://www.flexibee.eu/api/dokumentace/ref/web-hooks',
        _('When the database AbraFlexi to change the POST HTTP request sent to all registered URL')
    )
);

$settingsForm->addItem(new \Ease\TWB5\LinkButton(
    "?hookurl=" . urlencode($webHookUrl),
    _('Target to FlexPlorer'),
    'success',
    ['class' => 'button button-xs']
));

$settingsForm->addInput(new \Ease\Html\Widgets\Toggle(
    'changesformat',
    'JSON',
    ['onText' => 'JSON', 'offText' => 'XML']
), _('Data format'));

$settingsForm->addInput(
    new \Ease\Html\Widgets\Toggle('hookurltest', 'skip'),
    _('Skip URL test'),
    null,
    _('Suppress URL functionality test')
);

$settingsForm->addInput(
    new \Ease\Html\InputNumberTag(
        'lastVersion',
        null,
        ['min' => 0, 'max' => $globalVersion]
    ),
    _('Last version'),
    $globalVersion,
    sprintf(
        _('Version of which will begin sending FOLLOW changes , ie. The next higher version . The default value is equal to the current global version ( globalVersion ) at the moment of registration Hook. Permissible values ​​are in the range [ 0 , % s ]'),
        $globalVersion
    )
);

$randstr = \Ease\Functions::randomString(30);
$settingsForm->addInput(
    new \Ease\Html\InputTextTag('secKey'),
    _('Security Code'),
    $randstr,
    sprintf(
        _('Any string (eg. %s) that will be sent with each change notifications in the HTTP header. Used to easily verify that include incoming notifications you registered Hook. Key name is in the HTTP header X-FB-Hook-SecKey .'),
        $randstr
    )
);

$settingsForm->addItem(new \Ease\TWB5\SubmitButton(
    _('Perform operation'),
    'warning'
));
$toolRow->addColumn(4, new \Ease\TWB5\Panel($settingsForm));

if ($chapistatus) {
    try {
        $hooks = $hooker->getFlexiData();
        if (!isset($hooks['message']) && is_array($hooks) && !empty(current($hooks))) {
            $hooksTable = new \Ease\Html\TableTag(null, ['class' => 'table']);
            $hooksTable->addRowHeaderColumns(array_merge(array_keys(current($hooks)), [_('Reset'), _('Test'), _('Remove')]));
            foreach ($hooks as $hookinfo) {
                $hookinfo[] = new \Ease\TWB5\LinkButton(
                    '?refresh=' . $hookinfo['id'],
                    '♻️',
                    'success'
                );
                $hookinfo[] = new \Ease\TWB5\LinkButton(
                    'fakechange.php?hookurl=' . $hookinfo['url'],
                    '📤',
                    'info',
                    ['title' => _('Test')]
                );
                $hookinfo[] = new \Ease\TWB5\LinkButton(
                    '?linkdel=' . $hookinfo['id'],
                    '🪦',
                    'danger'
                );
                $hookinfo['url'] = new \Ease\Html\ATag(
                    $hookinfo['url'],
                    $hookinfo['url']
                );

                $hooksTable->addRowColumns($hookinfo);
            }

            $toolRow->addColumn(
                8,
                new \Ease\TWB5\Panel(_('Webhooks registered'), 'info', $hooksTable)
            );
        }
    } catch (\AbraFlexi\Exception $exc) {
        echo $hooker->addStatusMessage($exc->getMessage(), 'error');
    }
}
$oPage->container->addItem(new \Ease\TWB5\Panel(
    _('ChangesAPI & WebHooks'),
    'info',
    $toolRow
));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
