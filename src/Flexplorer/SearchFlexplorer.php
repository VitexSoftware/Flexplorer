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

/**
 * Description of SearchFlexplorer.
 *
 * @author vitex
 */
class SearchFlexplorer extends \Flexplorer\Flexplorer
{
    private $occurencies = [];

    /**
     * Search All.
     *
     * @param type $query
     */
    public function __construct($query)
    {
        if (\is_array($query)) {
            if (\array_key_exists('stitky', $query)) {
                $queryString = 'stitky=\'code:'.$query['stitky'].'\'';
            }

            if (\array_key_exists('evidence', $query)) {
                parent::__construct($query['evidence']);
                $this->defaultUrlParams = [];
                $this->occurencies[$query['evidence']] = $this->getFlexiData(isset($queryString) ? '('.$queryString.')' : $query);
            } else {
                $occurencies = [];
                $this->defaultUrlParams = [];
                parent::__construct();

                foreach (\AbraFlexi\EvidenceList::$evidences as $evidenceName => $evidenceInfo) {
                    if (
                        isset(\AbraFlexi\Properties::${$evidenceName}) && \array_key_exists(
                            'stitky',
                            \AbraFlexi\Properties::${$evidenceName},
                        ) && !($evidenceName === 'prodejka')
                    ) {
                        $this->setEvidence($evidenceName);
                        $occurencies[$evidenceName] = $this->getFlexiData($queryString ?? $query);
                    }
                }

                $this->occurencies = $occurencies;
            }
        }
    }

    public function getData()
    {
        if ($this->occurencies) {
            $data = $this->occurencies;
        } else {
            $data = parent::getData();
        }

        return $data;
    }
}
