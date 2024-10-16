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
 * Description of ModulesOverview.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class ModulesOverview extends \Ease\Html\UlTag
{
    /**
     * Language Dependencies.
     */
    public array $translations = [];

    public function __construct($ulContents = null)
    {
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
            'MZD' => _('Wages'),
            'CRM' => _('CRM'),
            'PPP' => _('Inquiries received'),
            'PPV' => _('Demands issued'),
            'NAP' => _('Bids Received'),
            'NAV' => _('bids received'),
            'OBP' => _('Orders Received'),
            'OBV' => _('Orders will issue'),
            'MAJ' => _('Assets'),
        ];

        parent::__construct(null, ['class' => 'list-group']);
        asort($this->translations);

        foreach ($this->translations as $code => $name) {
            if (\array_key_exists($code, $ulContents)) {
                $type = 'success';
            } else {
                $type = 'default';
            }

            $this->addItemSmart(new \Ease\TWB5\Badge($code.' - '.$name, $type), ['class' => 'list-group-item']);
        }
    }
}
