<?php

namespace Flexplorer;

/**
 * Flexplorer - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer;

require_once 'includes/Init.php';

$oPage->onlyForLogged();


$oPage->addItem(new ui\PageTop(_('Seznam evidencí flexibee')));

$evidenceLister = new \FlexiPeeHP\EvidenceList();
$evidencies     = $evidenceLister->getAllFromFlexiBee();

$evlist = new \Ease\Html\TableTag(NULL, ['class' => 'table']);
$evlist->addRowHeaderColumns([_('Jméno evidence'), _(''), _(''), _('')]);

foreach ($evidencies['evidences']['evidence'] as $evidence) {
    $evrow         = [];
    $evrow['name'] = $evidence['evidenceName'];
    $evlist->addRowColumns($evrow);
}

$oPage->container->addItem($evlist);

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
