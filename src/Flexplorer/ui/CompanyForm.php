<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of CompanyForm
 *
 * @author vitex
 */
class CompanyForm extends \Ease\TWB\Form {

    /**
     *
     * @param \AbraFlexi\Company $company
     */
    public function __construct($company) {
        parent::__construct('company');

        $this->addInput(new \Ease\Html\InputTextTag('nazev',
                        $company->getDataValue('nazev')), _('Company name'),
                _('My Company'));

//        $this->addInput(new \Ease\Html\InputTextTag('country',
//                $company->getDataValue('country')), _('Country'), _('Country'));
//
//        $this->addInput(new \Ease\Html\InputTextTag('country',
//                $company->getDataValue('use-demo')), _('Use demo'),
//            _('Fill database with demo content'));


        $this->addItem(new \Ease\TWB\SubmitButton(_('Save'), 'success',
                        ['onClick' => "$('#Preloader').css('visibility', 'visible');"]));
    }

    public function finalize() {
        WebPage::singleton()->body->setTagClass('fuelux');
        WebPage::singleton()->body->addItem(new FXPreloader('Preloader'));
        parent::finalize();
    }

}
