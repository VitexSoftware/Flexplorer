<?php

declare(strict_types=1);

/**
 * This file is part of the Flexplorer package
 *
 * github.com/VitexSoftware/Flexplorer
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

$changeFile = HookReciever::getSaveDir().'/'.basename($change ?: 'flexplorer_changes.json');

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

    if (($responseCode === 200) && !\strlen($responseBody)) {
        $prober->addStatusMessage(_('WebHook successfully triggered'), 'success');
    } elseif (\strlen($responseBody)) {
        $prober->addStatusMessage(
            _('WebHook returns non empty response'),
            'warning',
        );
    }
}

$lister = new \AbraFlexi\EvidenceList();
$flexidata = $lister->getFlexiData();

if (\count($flexidata)) {
    foreach ($flexidata as $evidenceData) {
        $evidenciesToMenu[$evidenceData['evidencePath']] = $evidenceData['evidenceName'];
    }

    asort($evidenciesToMenu);
}

$oPage->addItem(new ui\PageTop(_('WebHook test')));

$changeTabs = new \Ease\TWB5\Tabs([], ['name' => 'changetabs']);

$toolRow = new \Ease\TWB5\Row();
$settingsForm = new \Ease\TWB5\Form(['name' => 'settings']);

// TODO \Ease\TWB5\Widgets\Toggle
$settingsForm->addInput(
    new \Ease\TWB5\Widgets\Toggle(
        'changesformat',
        true,
        'JSON',
        ['onText' => 'JSON', 'offText' => 'XML', 'disabled' => true],
    ),
    _('Data format'),
);

$settingsForm->addInput(
    new \Ease\Html\InputNumberTag(
        'lastversion',
        $lastversion,
        ['min' => 0],
    ),
    _('Last version'),
    $lastversion,
    _('Which version of the changes will begin sending following changes'),
);

$settingsForm->addInput(new \Ease\Html\SelectTag(
    'evidence',
    $evidenciesToMenu,
    (string) $evidence,
), _('Evidence'));

$settingsForm->addInput(new \Ease\Html\SelectTag(
    'operation',
    ['create' => 'Create', 'update' => 'Update', 'delete' => 'Delete'],
    (string) $operation,
), _('Operation'), null);

$settingsForm->addInput(
    new \Ease\Html\InputNumberTag('id', $id, ['min' => 0]),
    _('Record number'),
    $id,
    _('Internal number of record edited'),
);

$settingsForm->addInput(
    new \Ease\Html\InputTextTag('extid', $extid),
    _('External number'),
    $extid,
    _('External number of record edited'),
);

$settingsForm->addItem(new \Ease\TWB5\SubmitButton(_('Build change'), 'warning'));
$toolRow->addColumn(4, new \Ease\TWB5\Container($settingsForm));

$hookForm = new \Ease\TWB5\Form(['name' => 'TriggerHook']);
$hookForm->addInput(
    new \Ease\Html\InputTextTag('hookurl', $hookurl),
    _('Web Hook'),
    'http://server/getchanges.php',
    [new \Ease\TWB5\LinkButton('changesapi.php', _('Choose Registered')),
        new \Ease\Html\ATag(
            'https://www.flexibee.eu/api/dokumentace/ref/web-hooks',
            _('When the database AbraFlexi is changed the POST HTTP request sent to all registered URL'),
        )],
);

$hookForm->addInput(new ui\JsonTextarea(
    'code',
    json_encode($changeData, \JSON_PRETTY_PRINT),
));
$hookForm->addItem(new \Ease\TWB5\SubmitButton(_('Send'), 'success'));

$toolRow->addColumn(8, new \Ease\TWB5\Card($hookForm));

if (empty($responseBody) === false) {
    $responseBlock = new \Ease\TWB5\Panel(
        new \Ease\Html\H1Tag($prober->lastResponseCode),
        'info',
    );

    $responseBlock->addItem(new \Ease\Html\DivTag('<pre><code class="'.$format.'">'.
            nl2br(htmlentities($prober->lastCurlResponse))
            .'</code></pre>'));

    $oPage->includeCss('css/github.css');
    $oPage->includeJavaScript('js/highlight.min.js');
    $oPage->addJavascript(<<<'EOD'
$('pre code').each(function(i, block) {
    hljs.highlightBlock(block);
  });
EOD);

    $changeTabs->addTab(_('Response'), $responseBlock);
}

$changeTabs->addTab(_('Request'), $toolRow);

$oPage->addItem(new \Ease\TWB5\Panel(
    _('WebHook probe'),
    'info',
    $changeTabs,
));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
