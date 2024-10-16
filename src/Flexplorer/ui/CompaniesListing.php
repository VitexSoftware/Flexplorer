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
 * Description of BackupsListing.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class CompaniesListing extends \Ease\Html\DivTag
{
    public \Ease\Html\TrTag $header;

    /**
     * Company data source.
     */
    public \AbraFlexi\Company $companer;
    public \Ease\Html\TableTag $contents;

    /**
     * Show basic directory listing.
     *
     * @param array $properties
     */
    public function __construct($properties = [])
    {
        parent::__construct(
            new \Ease\Html\H1Tag(_('Companies Listing')),
            $properties,
        );
        $this->contents = new \Ease\Html\TableTag('', ['class' => 'table']);
        $this->companer = new \AbraFlexi\Company(null, ['company' => null]);

        foreach ($this->getListing() as $companyInfo) {
            $this->contents->addRowColumns($this->companyRow($companyInfo));
        }

        $this->header = $this->contents->addRowHeaderColumns([_('Name'), _('Age'), _('Database')]);
        $this->addItem($this->contents);
    }

    public function getListing()
    {
        $companies = [];
        $companiesRaw = $this->companer->getFlexiData();

        if (\count($companiesRaw)) {
            $companies = \Ease\Functions::reindexArrayBy($companiesRaw, 'dbNazev');
        }

        return $companies;
    }

    public function companyRow($companyDataRaw)
    {
        $companyData['name'] = new \Ease\Html\ATag(
            'company.php?company='.$companyDataRaw['dbNazev'],
            $companyDataRaw['nazev'],
        );

        $created = \AbraFlexi\RO::flexiDateTimeToDateTime($companyDataRaw['createDt'])->getTimestamp();
        $companyData['created'] = \AbraFlexi\RO::flexiDateTimeToDateTime($companyDataRaw['createDt'])->format('d.m. Y').' ('._('before').' '.new \Ease\Html\Widgets\LiveAge($created).')';
        $companyData['databaze'] = new CopyToClipBoard(new \Ease\Html\InputTextTag(
            'dbNazev',
            $companyDataRaw['dbNazev'],
            ['id' => 'dbNazev'.$companyDataRaw['dbNazev'], 'readonly'],
        ));

        return $companyData;
    }
}
