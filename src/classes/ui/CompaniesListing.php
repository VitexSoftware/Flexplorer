<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template company, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of BackupsListing
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class CompaniesListing extends \Ease\Html\DivTag
{
    /**
     *
     * @var \Ease\Html\ThTag
     */
    public $header = null;

    /**
     * Company data source
     * @var \FlexiPeeHP\Company
     */
    public $companer = null;

    /**
     *
     * @var \Ease\Html\TableTag
     */
    public $contents = null;

    /**
     * Show basic directory listing
     *
     * @param string $backupDir
     * @param string $regex
     * @param array $properties
     */
    public function __construct($properties = [])
    {
        parent::__construct(new \Ease\Html\H1Tag(_('Companies Listing')),
            $properties);
        $this->contents = new \Ease\Html\TableTag('', ['class' => 'table']);
        $this->companer = new \FlexiPeeHP\Company(null,['company'=>null]);
        foreach ($this->getListing() as $companyInfo) {
            $this->contents->addRowColumns($this->companyRow($companyInfo));
        }
        $this->header = $this->contents->addRowHeaderColumns([_('Name'), _('Age'),
            _('Database')]);
        $this->addItem($this->contents);
    }

    public function getListing()
    {
        $companies = [];
        $companiesRaw = $this->companer->getFlexiData();
        if (count($companiesRaw)) {
            $companies = $this->reindexArrayBy($companiesRaw, 'dbNazev');
        }
        return $companies;
    }

    public function companyRow($companyDataRaw)
    {
        $companyData['name']     = new \Ease\Html\ATag('company.php?company='.$companyDataRaw['dbNazev'],
            $companyDataRaw['nazev']);

        $created                = \FlexiPeeHP\FlexiBeeRO::flexiDateTimeToDateTime($companyDataRaw['createDt'])->getTimestamp();
        $companyData['created'] = strftime('%a %d. %m. %Y  - %X', $created).' '.'('._('before').' '.new \Ease\ui\LiveAge($created).')';
        $companyData['databaze'] = new CopyToClipBoard(new \Ease\Html\InputTextTag('dbNazev',
                $companyDataRaw['dbNazev'], ['id' => 'dbNazev', 'readonly']));

        return $companyData;
    }
}
