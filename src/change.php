<?php

namespace Flexplorer;

/**
 * Flexplorer - Show WebHook data recieved.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016-2017 Vitex Software
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$delete = $oPage->getRequestValue('delete');

if ($delete) {
    if (unlink(HookReciever::getSaveDir().'/'.basename($delete))) {
        $oPage->addStatusMessage(sprintf(_('File witch Change %s was deleted'),
                $delete), 'success');
        $oPage->redirect('changes.php');
    } else {
        $oPage->addStatusMessage(sprintf(_('File with Change %s was not deleted'),
                $delete), 'warning');
    }
}



$file                         = $oPage->getRequestValue('file');
$changeFile                   = HookReciever::getSaveDir().'/'.basename($file);
$sender                       = new Flexplorer();
$sender->lastResponseCode     = 200;
$sender->lastCurlResponse     = json_encode(json_decode(file_get_contents($changeFile)),
    JSON_PRETTY_PRINT);
$sender->info['content_type'] = 'json';
$sender->info['url']          = 'file://'.$file;

$oPage->addItem(new ui\PageTop(_('Changes recieved').': '.$file));


$oPage->container->addItem(new ui\ShowResponse($sender));

$testForm = new \Ease\TWB\Form('probechange', 'fakechange.php');

$testForm->addItem(new \Ease\Html\InputHiddenTag('changefile', $file));

$optionsRow = new \Ease\TWB\Row();
$optionsRow->addColumn(6, new ui\WebHookSelect('hookurl'));
$optionsRow->addColumn(2,
    new \Ease\TWB\SubmitButton(new \Ease\TWB\GlyphIcon('flash').' '._('Probe'),
        'success'));
$optionsRow->addColumn(2);
$optionsRow->addColumn(2,
    new \Ease\TWB\LinkButton('change.php?delete='.$file,
        new \Ease\TWB\GlyphIcon('trash').' '._('Delete'), 'danger'));

$testForm->addItem($optionsRow);


$oPage->container->addItem($testForm);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
