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

$hookurl     = $oPage->getRequestValue('hookurl');
$evidence    = $oPage->getRequestValue('evidence');
$operation   = $oPage->getRequestValue('operation');
$extid       = $oPage->getRequestValue('extid');
$lastversion = $oPage->getRequestValue('lastversion', 'int');
$id          = $oPage->getRequestValue('id', 'int');
$format      = 'json';

$change = ['winstrom' => ['@globalVersion' => $lastversion, 'changes' => ['@evidence' => $evidence,
            '@in-version' => $lastversion,
            '@operation' => $operation, 'id' => $id, 'external-ids' => [$extid]]]];

$responseBody = null;
$responseCode = null;
if ($oPage->isPosted()) {
    $prober             = new \FlexiPeeHP\FlexiBeeRW();
    $prober->postFields = json_encode($change);
    $responseCode       = $prober->doCurlRequest($hookurl, 'POST', $format);
    $responseBody       = $prober->lastCurlResponse;

    if (($responseCode == 200) && !strlen($responseBody)) {
        $prober->addStatusMessage(_('WebHook successfully triggered'), 'success');
    } else if (strlen($responseBody)) {
        $prober->addStatusMessage(_('WebHook returns non empty response'),
            'warning');
    }
}

$lister    = new \FlexiPeeHP\EvidenceList();
$flexidata = $lister->getFlexiData();

if (count($flexidata)) {
    foreach ($flexidata['evidences']['evidence'] as $evidence) {
        $evidenciesToMenu[$evidence['evidencePath']] = $evidence['evidenceName'];
    }
    asort($evidenciesToMenu);
}

$oPage->addItem(new ui\PageTop(_('Test Web Hooku')));

$changeTabs = new \Ease\TWB\Tabs('changetabs');


$toolRow      = new \Ease\TWB\Row();
$settingsForm = new \Ease\TWB\Form('settings');


$settingsForm->addInput(new ui\TWBSwitch('changesformat', true, 'JSON',
    ['onText' => 'JSON', 'offText' => 'XML', 'disabled' => true]),
    _('Formát dat'));

$settingsForm->addInput(new \Ease\Html\InputNumberTag('lastversion',
    $lastversion, ['min' => 0]), _('Poslední verze'), $lastversion,
    _('Verze od které započne posílání následujích změn'));

$settingsForm->addInput(new \Ease\Html\Select('evidence', $evidenciesToMenu,
    $evidence), _('Evidence'));


$settingsForm->addInput(new \Ease\Html\Select('operation',
    ['create' => 'Create', 'update' => 'Update', 'delete' => 'Delete'],
    $operation), _('Operace'), null);

$settingsForm->addInput(new \Ease\Html\InputNumberTag('id', $id, ['min' => 0]),
    _('Číslo záznamu'), $id, _('Interní číslo editovaného záznamu'));

$settingsForm->addInput(new \Ease\Html\InputTextTag('extid', $extid),
    _('Ext. Číslo záznamu'), $extid, _('Externí číslo editovaného záznamu'));


$settingsForm->addItem(new \Ease\TWB\SubmitButton(_('Sestavit změnu'), 'warning'));
$toolRow->addColumn(4, new \Ease\TWB\Well($settingsForm));


$hookForm = new \Ease\TWB\Form('TriggerHook');
$hookForm->addInput(new \Ease\Html\InputTextTag('hookurl', $hookurl),
    _('Web Hook'), 'http://server/getchanges.php',
    [new \Ease\TWB\LinkButton('changesapi.php', _('Choose Registered')),
    new \Ease\Html\ATag('https://www.flexibee.eu/api/dokumentace/ref/web-hooks',
        _('Když dojde v databázi FlexiBee ke změně, je odeslán POST HTTP request na všechna zaregistrovaná URL'))]
);

$hookForm->addInput(new ui\JsonTextarea('code', json_encode($change)));
$hookForm->addItem(new \Ease\TWB\SubmitButton(_('Odeslat'), 'success'));

$toolRow->addColumn(8, new \Ease\TWB\Well($hookForm));

if (strlen($responseBody)) {

    $responseBlock = new \Ease\TWB\Panel(new \Ease\Html\H1Tag($prober->lastResponseCode),
        'info');


    $responseBlock->addItem('<pre><code class="'.$format.'">'.
        nl2br(htmlentities($prober->lastCurlResponse))
        .'</code></pre>');


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
