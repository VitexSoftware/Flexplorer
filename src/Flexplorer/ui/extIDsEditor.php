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
 * Description of extIDsEditor.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class extIDsEditor extends \Ease\TWB5\Container
{
    public function __construct($engine)
    {
        $this->engine = $engine;
        parent::__construct(new \Ease\Html\InputHiddenTag(
            'id',
            $this->engine->getDataValue('id'),
        ));

        $this->addItem(new \Ease\Html\InputHiddenTag(
            'evidence',
            $this->engine->getEvidence(),
        ));

        $externalIDs = $this->engine->getDataValue('external-ids');

        if (!empty($externalIDs)) {
            foreach ($externalIDs as $externalID) {
                if (!\strlen($externalID)) {
                    continue;
                }

                $idParts = explode(':', $externalID);

                if (!isset($idParts[2])) {
                    $idParts[2] = '';
                }

                $extIDrow = new \Ease\TWB5\Row();
                $extIDrow->addColumn(
                    4,
                    new \Ease\TWB5\Checkbox(
                        'deleteExtID['.$idParts[1].']',
                        $externalID,
                        _('Remove'),
                    ),
                );
                $extIDrow->addColumn(
                    8,
                    new \Ease\TWB5\FormGroup(
                        $idParts[1],
                        new \Ease\Html\InputTextTag(
                            'external-ids['.$idParts[1].']',
                            $idParts[2],
                            ['maxlength' => '20'],
                        ),
                        $idParts[1],
                        $externalID,
                    ),
                );
                $this->addItem($extIDrow);
            }
        }

        $this->addItem(new \Ease\TWB5\FormGroup(
            _('New'),
            new \Ease\Html\InputTextTag('external-ids[]'),
            'ext:..',
            new \Ease\Html\ATag(
                'https://www.flexibee.eu/api/dokumentace/ref/identifiers/',
                _('External IDs'),
            ),
        ));

        $this->addItem(new \Ease\TWB5\SubmitButton(_('OK').' 💾', 'success'));
    }
}
