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
 * Description of RecordDownloader.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class RecordDownloader extends \Ease\TWB5\Panel
{
    /**
     * @param \Flexplorer\Flexplorer $engine
     */
    public function __construct($engine)
    {
        parent::__construct(_('Record downloads'), 'default');
        $evidence = $engine->getEvidence();
        $info = $engine->getEvidenceInfo();

        if (\array_key_exists('formats', $info)) {
            foreach ($info['formats'] as $format => $suffix) {
                $this->addItem(new \Ease\TWB5\LinkButton(
                    'getinformat.php?format='.$suffix.'&id='.$engine->getRecordID().'&evidence='.$evidence,
                    $format,
                    'success',
                ));
            }

            if (!\array_key_exists('PDF', $info['formats'])) {
                $this->addItem(new \Ease\TWB5\LinkButton(
                    'getinformat.php?format=pdf&id='.$engine->getRecordID().'&evidence='.$evidence,
                    'PDF',
                ));
            }
        } else {
            $this->addItem(sprintf(
                _('No availble formats info for evidence %s is not set'),
                $evidence,
            ));
        }
    }
}
