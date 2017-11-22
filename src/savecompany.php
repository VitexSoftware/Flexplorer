<?php

namespace Flexplorer;

/**
 * Flexplorer - Create new company.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';


$oPage->onlyForLogged();

$company = $_SESSION['company'];

$saveTo = $oPage->getRequestValue('saveto');

$defaultSaveTo = $company.'-'.date('Y-m-d-Hi').'.winstrom-backup';

$saver = new \FlexiPeeHP\Company(['dbNazev' => $company]);
if (!empty($saveTo)) {
    if ($saver->saveBackupTo(constant('BACKUP_DIRECTORY').$saveTo)) {
        $saver->addStatusMessage(_('backup saved'), 'success');
    }

    $oPage->redirect('company.php?company='.$company);
} else {
    $saver->addStatusMessage(_('Please enter backup file name'), 'warning');
}


$oPage->addItem(new ui\PageTop(_('Company Backup')));

$saveForm = new \Ease\TWB\Form('Save');

$saveForm->addInput(new \Ease\Html\InputTextTag('saveto',
        empty($saveTo) ? $defaultSaveTo : $saveTo ));

$saveForm->addItem(new \Ease\TWB\SubmitButton(new \Ease\TWB\GlyphIcon('floppy-save').' '._('Save'),
        'success',
        ['onClick' => "$('#Preloader').css('visibility', 'visible');"]));


$saveRow = new \Ease\TWB\Row();
$saveRow->addColumn(6, $saveForm);

$saveRow->addColumn(6,
    new ui\BackupsListing(constant('BACKUP_DIRECTORY'),
        $company.'.*\.winstrom-backup'));

$oPage->container->addItem(new \Ease\TWB\Panel(_('Save Company As'), 'success',
        $saveRow));

$oPage->addItem(new ui\PageBottom());

\Ease\Shared::webPage()->body->setTagClass('fuelux');
\Ease\Shared::webPage()->body->addItem(new ui\FXPreloader('Preloader'));


$oPage->draw();

