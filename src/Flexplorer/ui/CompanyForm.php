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

namespace Flexplorer\ui;

/**
 * Description of CompanyForm.
 *
 * @author vitex
 */
class CompanyForm extends \Ease\TWB5\Form
{
    public function __construct(\AbraFlexi\Company $company)
    {
        parent::__construct(['id' => 'company']);

        $this->addInput(
            new \Ease\Html\InputTextTag(
                'nazev',
                $company->getDataValue('nazev'),
            ),
            _('Company name'),
            _('My Company'),
        );

        //        $this->addInput(new \Ease\Html\InputTextTag('country',
        //                $company->getDataValue('country')), _('Country'), _('Country'));
        //
        //        $this->addInput(new \Ease\Html\InputTextTag('country',
        //                $company->getDataValue('use-demo')), _('Use demo'),
        //            _('Fill database with demo content'));

        $this->addItem(new \Ease\TWB5\SubmitButton(
            _('Save'),
            'success',
            ['onClick' => "$('#Preloader').css('visibility', 'visible');"],
        ));
    }

    public function finalize(): void
    {
        WebPage::singleton()->body->setTagClass('fuelux');
        WebPage::singleton()->body->addItem(new FXPreloader('Preloader'));
        parent::finalize();
    }
}
