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

class EvidenceProperties extends \Ease\Html\TableTag
{
    /**
     * Show evidence columns properties.
     *
     * @param string     $evidence to describe
     * @param string     $hlcolumn to highlight
     * @param null|mixed $cond
     */
    public function __construct($evidence, $hlcolumn = null, $cond = null)
    {
        parent::__construct(null, ['class' => 'table table-hover']);
        $this->setTagId('structOf'.$evidence->getEvidence().$cond);

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

        $this->addRowHeaderColumns($columns);

        if (null !== $cond) {
            if ($cond[0] === '!') {
                $cond = substr($cond, 1);
                $req = 'false';
            } else {
                $req = 'true';
            }
        }

        foreach ($proprtiesData as $propName => $propValues) {
            if (null !== $cond) {
                if (isset($propValues[$cond])) {
                    if ($propValues[$cond] !== $req) {
                        continue;
                    }
                }
            }

            foreach ($columns as $value) {
                if (isset($propValues[$value])) {
                    switch ($value) {
                        case 'fkName':
                            if (isset($propValues['url'])) {
                                $tmp = explode('/', $propValues['url']);
                                $revidence = 'evidence.php?evidence='.end($tmp);
                                $props[$value] = '<a href="'.$revidence.'" title="'.$propValues['fkName'].'">🔗'.$propValues[$value].'</a> ';
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
                                $label = new \Ease\TWB5\Badge($defineValue['@key'], 'default', ['title' => $defineValue['$']]);

                                if (\array_key_exists($value, $props)) {
                                    $props[$value] .= $label->__toString();
                                } else {
                                    $props[$value] = $label->__toString();
                                }
                            }

                            break;

                        default:
                            switch ($propValues[$value]) {
                                case 'true':
                                    $props[$value] = '❌';

                                    break;
                                case 'false':
                                    $props[$value] = '✅';

                                    break;

                                default:
                                    if (isset($_SESSION['searchQuery']) && is_string($propValues[$value])) {
                                        $term = $_SESSION['searchQuery'];
                                        $props[$value] = str_ireplace(
                                            $term,
                                            "<strong>{$term}</strong>",
                                            $propValues[$value],
                                        );
                                    } elseif (is_array($propValues[$value])) {
                                        $props[$value] = json_encode($propValues[$value]);
                                    } else {
                                        $props[$value] = $propValues[$value];
                                    }

                                    break;
                            }

                            break;
                    }
                } else {
                    $props[$value] = '';
                }
            }

            if ($propName === $hlcolumn) {
                $this->addRowColumns(
                    $props,
                    ['style' => 'background-color: yellow'],
                );
            } else {
                $this->addRowColumns($props);
            }
        }
    }

    public function finalize(): void
    {
        $this->includeJavaScript('js/jquery.fixedheadertable.min.js');
        $this->includeCss('css/defaultTheme.css');
        $this->addJavaScript('$("#'.$this->getTagID().<<<'EOD'
").fixedHeaderTable({
        height: $( window ).height()-100,
	autoShow: true
});
EOD);

        parent::finalize();
    }
}
