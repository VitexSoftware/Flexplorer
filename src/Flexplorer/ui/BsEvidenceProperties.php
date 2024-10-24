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

class BsEvidenceProperties extends BsGrid
{
    public function __construct($evidence)
    {
        parent::__construct(null, ['class' => 'table table-hover']);

        if (\is_string($evidence)) {
            $properter = new \AbraFlexi\RO();
            $properter->setEvidence($evidence.'/properties');
            $proprtiesData = $properter->getFlexiData();
            $proprtiesData = $proprtiesData['properties']['property'];
        } else {
            $proprtiesData = $evidence->evidenceStructure;
        }

        foreach ($proprtiesData as $properties) {
            foreach ($properties as $propertyName => $propertyValue) {
                if (!isset($columns[$propertyName])) {
                    $columns[$propertyName] = $propertyName;
                }
            }
        }

        //        $this->addRowHeaderColumns($columns);

        foreach ($proprtiesData as $propName => $propValues) {
            foreach ($columns as $value) {
                if (isset($propValues[$value])) {
                    switch ($value) {
                        case 'fkName':
                            if (isset($propValues['url'])) {
                                $tmp = explode('/', $propValues['url']);
                                $revidence = 'evidence.php?evidence='.end($tmp);
                                $props[$value] = '<a href="'.$revidence.'">'.\Ease\TWB5\Part::glyphIcon(
                                    'link',
                                    ['title' => $propValues['fkName']],
                                )->__toString().$propValues[$value].'</a> ';
                            } else {
                                $props[$value] = $propValues[$value];
                            }

                            break;
                        case 'evidenceVariants':
                            if (\is_array($propValues[$value]['evidenceVariant'])) {
                                $props[$value] = implode(
                                    ', ',
                                    $propValues[$value]['evidenceVariant'],
                                );
                            }

                            break;
                        case 'url':
                            $props[$value] = '<a href="'.$propValues[$value].'">'.$propValues[$value].'</a>';

                            break;
                        case 'values':
                            foreach ($propValues[$value]['value'] as $defineKey => $defineValue) {
                                $label = new \Ease\TWB5\Badge(
                                    $defineValue['@key'],
                                    ['title' => $defineValue['$']],
                                );
                                $props[$value] .= $label->__toString();
                            }

                            break;

                        default:
                            switch ($propValues[$value]) {
                                case 'true':
                                    $props[$value] = \Ease\TWB5\Part::glyphIcon('unchecked')->__toString();

                                    break;
                                case 'false':
                                    $props[$value] = \Ease\TWB5\Part::glyphIcon('check')->__toString();

                                    break;

                                default:
                                    $props[$value] = $propValues[$value];

                                    break;
                            }

                            break;
                    }
                } else {
                    $props[$value] = '';
                }
            }
            // $this->addRowColumns($props);
        }
    }
}
