<?php

/**
 * Flexplorer - Label Switches
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of LabelSwitches
 *
 * @author vitex
 */
class LabelSwitches extends \Ease\Html\DivTag
{
    /**
     * FlexiBee Label Switches
     *
     * @param \FlexiPeeHP\FlexiBeeRW $flexibee
     * @param array $properties
     */
    public function __construct($flexibee, $properties = [])
    {
        $allLabels     = \FlexiPeeHP\Stitek::getAvailbleLabels($flexibee);
        $enabledLabels = \FlexiPeeHP\Stitek::getLabels($flexibee);
        if (empty($enabledLabels)) {
            $enabledLabels = [];
        }
        $enabledLabels = array_flip($enabledLabels);
        if (!isset($properties['id'])) {
            $properties['id'] = $flexibee->getEvidence().$flexibee->getMyKey().'labed';
        }
        parent::__construct(null, $properties);
        foreach ($allLabels as $code => $title) {
            $twbsw = $this->addItem(new \Ease\ui\TWBSwitch($code,
                array_key_exists($code, $enabledLabels), 1,
                ['onText' => $code, 'offText' => $title, 'onColor' => 'success',
                'offColor' => 'default', 'labelWidth' => 10, 'handleWidth' => 200
            ]));
            $twbsw->setTagProperties(['data-evidence' => $flexibee->getEvidence(),
                'data-record' => $flexibee->getMyKey()]);
        }
    }

    public function finalize()
    {
        \Ease\TWB\Part::twBootstrapize();
        $this->addJavaScript('

$(\'#'.$this->getTagID().' input\').on(\'switchChange.bootstrapSwitch\', function(event, state) {

$.ajax({
   url: \'labed.php\',
        data: {
                evidence: $(this).attr("data-evidence"),
                record: $(this).attr("data-record"),
                label: $(this).attr("name"),
                state: state
        },
        error: function() {
            console.log("not saved");
        },

        success: function(data) {
            console.log("saved");
        },
            type: \'POST\'
        });
});
');
    }

}
