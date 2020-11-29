<?php

namespace Flexplorer;

/**
 * Flexplorer - Fake Change.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$hookurl = $oPage->getRequestValue('hookurl');
$evidence = $oPage->getRequestValue('evidence');
$operation = $oPage->getRequestValue('operation');
$extid = $oPage->getRequestValue('extid');
$lastversion = $oPage->getRequestValue('lastversion', 'int');
$id = $oPage->getRequestValue('id', 'int');
$change = $oPage->getRequestValue('changefile');
$format = 'json';

$changeFile = HookReciever::getSaveDir() . '/' . basename($change);


$changeData = ['winstrom' => ['@globalVersion' => $lastversion, 'changes' => ['@evidence' => $evidence,
            '@in-version' => $lastversion,
            '@operation' => $operation, 'id' => $id, 'external-ids' => [$extid]]]];

$responseBody = null;
$responseCode = null;
if ($oPage->isPosted() || (!empty($change) && file_exists($changeFile))) {
    $prober = new \AbraFlexi\RW();

    if (empty($change)) {
        $prober->postFields = json_encode($changeData);
    } else {
        $prober->postFields = file_get_contents($changeFile);
        $changeData = json_decode($prober->postFields);
    }

    $responseCode = $prober->doCurlRequest($hookurl, 'POST', $format);
    $responseBody = $prober->lastCurlResponse;

    if (($responseCode == 200) && !strlen($responseBody)) {
        $prober->addStatusMessage(_('WebHook successfully triggered'), 'success');
    } else if (strlen($responseBody)) {
        $prober->addStatusMessage(_('WebHook returns non empty response'),
                'warning');
    }
}

$lister = new \AbraFlexi\EvidenceList();
$flexidata = $lister->getFlexiData();

if (count($flexidata)) {
    foreach ($flexidata as $evidence) {
        $evidenciesToMenu[$evidence['evidencePath']] = $evidence['evidenceName'];
    }
    asort($evidenciesToMenu);
}

$oPage->addItem(new ui\PageTop(_('WebHook test')));

$changeTabs = new \Ease\TWB\Tabs('changetabs');


$toolRow = new \Ease\TWB\Row();
$settingsForm = new \Ease\TWB\Form('settings');


$settingsForm->addInput(new \Ease\ui\TWBSwitch('changesformat', true, 'JSON',
                ['onText' => 'JSON', 'offText' => 'XML', 'disabled' => true]),
        _('Data format'));

$settingsForm->addInput(new \Ease\Html\InputNumberTag('lastversion',
                $lastversion, ['min' => 0]), _('Last version'), $lastversion,
        _('Which version of the changes will begin sending following changes'));

$settingsForm->addInput(new \Ease\Html\Select('evidence', $evidenciesToMenu,
                $evidence), _('Evidence'));


$settingsForm->addInput(new \Ease\Html\Select('operation',
                ['create' => 'Create', 'update' => 'Update', 'delete' => 'Delete'],
                $operation), _('Operation'), null);

$settingsForm->addInput(new \Ease\Html\InputNumberTag('id', $id, ['min' => 0]),
        _('Record number'), $id, _('Internal number of record edited'));

$settingsForm->addInput(new \Ease\Html\InputTextTag('extid', $extid),
        _('External number'), $extid, _('External number of record edited'));


$settingsForm->addItem(new \Ease\TWB\SubmitButton(_('Build change'), 'warning'));
$toolRow->addColumn(4, new \Ease\TWB\Well($settingsForm));


$hookForm = new \Ease\TWB\Form('TriggerHook');
$hookForm->addInput(new \Ease\Html\InputTextTag('hookurl', $hookurl),
        _('Web Hook'), 'http://server/getchanges.php',
        [new \Ease\TWB\LinkButton('changesapi.php', _('Choose Registered')),
            new \Ease\Html\ATag('https://www.abraflexi.eu/api/dokumentace/ref/web-hooks',
                    _('When the database AbraFlexi is changed the POST HTTP request sent to all registered URL'))]
);

$hookForm->addInput(new ui\JsonTextarea('code',
                json_encode($changeData, JSON_PRETTY_PRINT)));
$hookForm->addItem(new \Ease\TWB\SubmitButton(_('Send'), 'success'));

$toolRow->addColumn(8, new \Ease\TWB\Well($hookForm));

if (strlen($responseBody)) {

    $responseBlock = new \Ease\TWB\Panel(new \Ease\Html\H1Tag($prober->lastResponseCode),
            'info');

    $responseBlock->addItem('<pre><code class="' . $format . '">' .
            nl2br(htmlentities($prober->lastCurlResponse))
            . '</code></pre>');


    $oPage->includeCss('css/github.css');
    $oPage->includeJavaScript('js/highlight.min.js');
    $oPage->addJavascript('$(\'pre code\').each(function(i, block) {
    hljs.highlightBlock(block);
  });');


    $changeTabs->addTab(_('Response'), $responseBlock);
}
$changeTabs->addTab(_('Request'), $toolRow);

$oPage->container->addItem(new \Ease\TWB\Panel(_('WebHook probe'), 'info',
                $changeTabs));


$oPage->addItem(new ui\PageBottom());

$oPage->draw();
