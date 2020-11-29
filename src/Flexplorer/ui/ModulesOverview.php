<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of ModulesOverview
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class ModulesOverview extends \Ease\Html\UlTag {

    /**
     * Language Dependencies
     * @var array 
     */
    public $translations = [];

    public function __construct($ulContents = null) {
        $this->translations = [
            'FAP' => _('Invoices Received'),
            'FAV' => _('Invoices Issued'),
            'BAN' => _('Banks'),
            'POK' => _('Checkout'),
            'SKL' => _('Warehouses'),
            'INT' => _('Internal Documents'),
            'PHL' => _('Receivables'),
            'ZAV' => _('Commitments'),
            'SES' => _('Sessions'),
            'MAJ' => _('Assets'),
            'MZD' => _('Wages'),
            'CRM' => _('CRM'),
            'PPP' => _('Inquiries received'),
            'PPV' => _('Demands issued'),
            'NAP' => _('Bids Received'),
            'NAV' => _('bids received'),
            'OBP' => _('Orders Received'),
            'OBV' => _('Orders will issue'),
            'MAJ' => _('Assets')
        ];

        parent::__construct(null, ['class' => 'list-group']);
        asort($this->translations);
        foreach ($this->translations as $code => $name) {
            if (array_key_exists($code, $ulContents)) {
                $type = 'success';
            } else {
                $type = 'default';
            }
            $this->addItemSmart(new \Ease\TWB\Label($type, $code . ' - ' . $name), ['class' => 'list-group-item']);
        }
    }

}
