<?php
/**
 * Flexplorer - Přehled vlastností evidence.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class EvidenceProperties extends \Ease\Html\TableTag
{

    /**
     * Show evidence columns properties
     *
     * @param string $evidence to describe
     * @param string $hlcolumn to highlight
     */
    public function __construct($evidence, $hlcolumn = null)
    {
        parent::__construct(null, ['class' => 'table table-hover']);
        $this->setTagId('structOf'.$evidence->getEvidence());
        if (is_string($evidence)) {
            $properter     = new \FlexiPeeHP\FlexiBeeRO();
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

        foreach ($proprtiesData as $propName => $propValues) {
            foreach ($columns as $value) {
                if (isset($propValues[$value])) {
                    switch ($value) {
                        case 'fkName':
                            if (isset($propValues['url'])) {
                                $tmp           = explode('/', $propValues['url']);
                                $revidence     = 'evidence.php?evidence='.end($tmp);
                                $props[$value] = '<a href="'.$revidence.'">'.\Ease\TWB\Part::glyphIcon('link',
                                        ['title' => $propValues['fkName']])->__toString().$propValues[$value].'</a> ';
                            } else {
                                $props[$value] = $propValues[$value];
                            }
                            break;
                        case 'evidenceVariants':
                            if (is_array($propValues[$value]['evidenceVariant'])) {
                                $props[$value] = implode(', ',
                                    $propValues[$value]['evidenceVariant']);
                            }
                            break;
                        case 'url':
                            $props[$value] = '<a href="'.$propValues[$value].'">'.$propValues[$value].'</a>';
                            break;
                        case 'values':
                            foreach ($propValues[$value]['value'] as $defineKey => $defineValue) {
                                $label = new \Ease\TWB\Badge(
                                    $defineValue['@key'],
                                    ['title' => $defineValue['$']]);
                                $props[$value] .= $label->__toString();
                            }

                            break;

                        default :

                            switch ($propValues[$value]) {
                                case 'true':
                                    $props[$value] = \Ease\TWB\Part::glyphIcon('unchecked')->__toString();
                                    break;
                                case 'false':
                                    $props[$value] = \Ease\TWB\Part::glyphIcon('check')->__toString();
                                    break;
                                default :
                                    if (isset($_SESSION['searchQuery'])) {
                                        $term          = $_SESSION['searchQuery'];
                                        $props[$value] = str_ireplace($term,
                                            "<strong>$term</strong>",
                                            $propValues[$value]);
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
            if ($propName == $hlcolumn) {
                $this->addRowColumns($props,
                    ['style' => 'background-color: yellow']);
            } else {
                $this->addRowColumns($props);
            }
        }
    }

    public function finalize()
    {
        $this->includeJavaScript('js/jquery.fixedheadertable.min.js');
        $this->includeCss('css/defaultTheme.css');
        $this->addJavaScript('$("#'.$this->getTagID().'").fixedHeaderTable({
        height: $( window ).height()-100,
	autoShow: true
}); ');

        return parent::finalize();
    }

}
