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

\define('K_PATH_IMAGES', \dirname(__DIR__).'/img/');

/**
 * Description of DataSource.
 *
 * @author vitex
 */
class DataSource extends \Ease\Brick
{
    public $charset = 'WINDOWS-1250//TRANSLIT';
    public $incharset = 'UTF-8';
    public $filename = 'export';
    public $columns = [];

    /**
     * Url pro odskok při editačních akcích.
     */
    public string $fallBackUrl = '';

    /**
     * Instance objektu webové stránky.
     */
    public EaseWebPage $webPage = null;

    /**
     * Data určena k znovunaplnění formuláře v případě chyby.
     */
    public array $fallBackData = [];

    /**
     * objekt poskytující data.
     */
    public Flexplorer $handledObejct = null;

    /**
     * PDF wrapper.
     */
    private TCPDF $pdf = null;

    /**
     * Titul exportu.
     */
    private string $title = '';
    private type $order = null;

    /**
     * Obtaing data for Grid.
     *
     * @param Flexplorer $handledObejct data providing object
     * @param type       $fallBackUrl
     */
    public function __construct($handledObejct, $fallBackUrl = null)
    {
        $this->handledObejct = $handledObejct;
        $this->keyword = $handledObejct->evidence;
        $this->keywordsInfo = $handledObejct->getColumnsInfo();

        $this->setBackUrl($fallBackUrl);
        $this->webPage = ui\WebPage::singleton();
        $this->title = $this->webPage->getRequestValue('title');

        if ($this->title) {
            $this->filename = preg_replace(
                '/[^0-9^a-z^A-Z^_^.]/',
                '',
                str_replace(' ', '_', $this->title),
            );
        }

        $cols = $this->webPage->getRequestValue('cols');

        if ($cols) {
            $col = explode('|', $cols);
            $names = $this->webPage->getRequestValue('names');
            $nam = explode('|', urldecode($names));
            $this->columns = array_combine($col, $nam);
        }

        $this->ajaxify();
    }

    /**
     * Vrací název použité evidence.
     *
     * @return string
     */
    public function getEvidence()
    {
        return $this->handledObejct->getEvidence();
    }

    public function setOrder($order): void
    {
        $this->order = $order;
    }

    /**
     * Nastaví URL pro znovuzobrazení stránky.
     *
     * @param type $url
     */
    public function setBackUrl($url): void
    {
        $this->fallBackUrl = $url;
    }

    /**
     * řešení.
     */
    public function ajaxify(): void
    {
        $action = $this->webPage->getRequestValue('action');

        if ($action) {
            if ($this->controlColumns()) {
                switch ($action) {
                    case 'delete':
                        if ($this->controlDeleteColumns()) {
                            $this->fallBackUrl = false;

                            if ($this->deleteFromSQL()) {
                                $this->webPage->addStatusMessage(_('Deleted'));
                            }
                        }

                        break;
                    case 'add':
                        if ($this->controlAddColumns()) {
                            if ($this->insertToSQL()) {
                                $this->webPage->addStatusMessage(
                                    _('Record was added'),
                                    'success',
                                );
                            } else {
                                $this->webPage->addStatusMessage(
                                    _('Record was not added'),
                                    'error',
                                );
                            }
                        }

                        break;
                    case 'edit':
                        if ($this->controlEditColumns()) {
                            if ($this->saveToSQL()) {
                                $this->webPage->addStatusMessage(
                                    _('Record was updated'),
                                    'success',
                                );
                            } else {
                                $this->webPage->addStatusMessage(
                                    _('Record update failed'),
                                    'error',
                                );
                            }
                        }

                        break;

                    default:
                        break;
                }
            }

            if ($this->fallBackUrl) {
                $this->webPage->redirect(\Ease\Page::arrayToUrlParams(
                    $this->fallBackData,
                    $this->fallBackUrl,
                ));
            }
        }
    }

    /**
     * Vrací celkový počet výsledků dotazu bez stránkování.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->handledObejct->rowCount;
    }

    /**
     * @param string $queryRaw
     * @param string $transform html|none
     *
     * @return array
     */
    public function getListing($queryRaw, $transform = 'html')
    {
        $page = $_REQUEST['page'] ?? 1;
        $rp = $_REQUEST['rp'] ?? 10;
        $sortname = $_REQUEST['sortname'] ?? $this->handledObejct->getkeyColumn();
        $sortorder = $_REQUEST['sortorder'] ?? 'desc';
        $conditions = $this->getWhere();
        $conditions['add-row-count'] = 'true';
        $start = (($page - 1) * $rp);

        $conditions['limit'] = $rp;
        $conditions['start'] = $start;
        $conditions['sort'] = $sortname;
        $conditions['dir'] = strtoupper($sortorder);
        $conditions['detail'] = 'full';

        $query = null;

        switch ($transform) {
            case 'html':
                $resultRaw = $this->handledObejct->htmlizeData($this->handledObejct->getFlexiData(
                    $query,
                    $conditions,
                ));

                break;

            default:
                $resultRaw = $this->handledObejct->getFlexiData($query);

                break;
        }

        if (!\count($this->columns)) {
            return $resultRaw;
        }

        $result = [];

        foreach ($resultRaw as $rrid => $resultRow) {
            foreach ($this->columns as $colKey => $colValue) {
                $result[$rrid][$colKey] = $resultRow[$colKey];
            }
        }

        return $result;
    }

    /**
     * Get Json type Response.
     *
     * @param string $queryRaw
     */
    public function getJson($queryRaw)
    {
        $rows = $this->webPage->getRequestValue('rows');

        if ($rows) {
            if ($rows[\strlen($rows) - 1] === ',') {
                $rows = substr($rows, 0, -1);
            }

            if ($this->order) {
                $order = ''.$this->order; // Sort
            } else {
                $order = '';
            }

            $transactions = $this->handledObejct->dblink->queryToArray(
                $queryRaw.' WHERE `'.$this->handledObejct->keyColumn.'` IN('.$rows.')'.$order,
                $this->handledObejct->getkeyColumn(),
            );
            $total = \count(explode(',', $rows));
        } else {
            $transactions = $this->getListing($queryRaw, 'html');
            $total = $this->getTotal();
        }

        $page = $_REQUEST['page'] ?? 1;
        $jsonData = ['page' => $page, 'total' => $total, 'rows' => []];

        if (\count($transactions)) {
            foreach ($transactions as $row) {
                if (\array_key_exists('id', $row)) {
                    $id = $row['id'];
                } else {
                    $id = current($row);
                }

                if (\is_array($id)) {
                    $id = current($id);
                }

                $entry = [
                    'id' => $id,
                    'cell' => $row,
                ];
                $jsonData['rows'][] = $entry;
            }
        }

        return json_encode($jsonData);
    }

    /**
     * Vrací CSV.
     *
     * @param type $queryRaw
     */
    public function getCsv($queryRaw): void
    {
        $transactions = self::getListing($queryRaw, 'csv');
        $this->getCSVFile($transactions);
    }

    /**
     * Vrací PDF.
     *
     * @param string $queryRaw
     */
    public function getPdf($queryRaw): void
    {
        $transactions = self::getListing($queryRaw);
        $this->pdfInit($this->title);
        $this->getPDFFile($transactions, array_values($this->columns));
    }

    /**
     * Vypíše výsledek SQL dotazu v požadovaném tvaru.
     *
     * @param type $queryRaw
     */
    public function output($queryRaw = null): void
    {
        if (null === $queryRaw) {
            $queryRaw = '?add-row-count=true';
        }

        switch (WebPage::singleton()->getRequestValue('export')) {
            default:
                header('Content-type: application/json');

                echo $this->getJson($queryRaw);

                break;
        }
    }

    /**
     * Init PDF exportu.
     *
     * @param string $title       nadpis stránky
     * @param char   $orientation P|L
     */
    public function pdfInit($title = null, $orientation = 'P'): void
    {
        $this->filename .= $title;

        // pdf object
        $this->pdf = new \TCPDF($orientation);

        // set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor(\Ease\Shared::user()->getUsername());
        $this->pdf->SetTitle($title);
        $this->pdf->SetSubject('');
        $this->pdf->SetKeywords($title);

        // set default header data
        $this->pdf->SetHeaderData('flexplorer-logo.png', 45, $title, 'Flexprer');
        // set header and footer fonts
        $this->pdf->setHeaderFont(['dejavusans', '', 8]);
        $this->pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $this->pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // ---------------------------------------------------------
        // set default font subsetting mode
        $this->pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $this->pdf->SetFont('dejavusans', '', 8, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $this->pdf->AddPage();

        // ---------------------------------------------------------
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
    }

    public function getPDFFromArray($array, $header = null)
    {
        $tbl = '<table>';

        $tbl .= '<tr>';

        foreach ($header as $h) {
            $tbl .= '<th style="font-weight: bold;">'.$h.'</th>';
        }

        $tbl .= '</tr>';

        foreach ($array as $row) {
            $tbl .= '<tr>';

            foreach ($row as $d) {
                $tbl .= '<td>'.$d.'</td>';
            }

            $tbl .= '</tr>';
        }

        $tbl .= '</table>';

        return $this->pdf->writeHTML($tbl, true, false, false, false, '');
    }

    public function getPDFFile($array, $header = null): void
    {
        // Output
        //        header("Content-type: text/x-csv");
        //        //header("Content-type: text/csv");
        //        //header("Content-type: application/csv");
        //        header("Cache-Control: maxage=3600");
        //        header("Pragma: public");
        //        header("Content-Disposition: attachment; filename = " . $this->filename . ".csv");
        $this->getPDFFromArray($array, $header);

        $this->pdf->Output($this->filename.'.pdf', 'I');
    }

    /**
     * Zkontroluje obecná vstupní data.
     *
     * @return bool
     */
    public function controlColumns()
    {
        return true;
    }

    /**
     * Zkontroluje splnění podmínek pro smazání záznamu.
     *
     * @return bool
     */
    public function controlDeleteColumns()
    {
        $id = WebPage::singleton()->getRequestValue('id');

        if ($id) {
            $this->setMyKey($id);

            return true;
        }

        return false;
    }

    /**
     * Zkontroluje podmínky pro přidání záznamu.
     *
     * @return bool
     */
    public function controlAddColumns()
    {
        return true;
    }

    /**
     * Zkontroluje podmínky pro editaci záznamu.
     *
     * @return bool
     */
    public function controlEditColumns()
    {
        $id = $this->webPage->getRequestValue('id');

        if ($id) {
            $this->setMyKey($id);

            return true;
        }

        return false;
    }

    /**
     * Vrací obecnou podmínku.
     *
     * @return string
     */
    public function getWhere()
    {
        $query = $_REQUEST['query'] ?? false;
        $qtype = $_REQUEST['qtype'] ?? false;

        if (empty($query)) {
            $where = [];
        } else {
            if ($qtype === 'external-ids') {
                $qtype = 'id';
            }

            $where = [$qtype => $query];
        }

        return $where;
    }
}
