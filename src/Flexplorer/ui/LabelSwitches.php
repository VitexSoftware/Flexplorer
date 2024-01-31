<?php

/**
 * Flexplorer - Label Switches
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
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
     * AbraFlexi Label Switches
     *
     * @param \AbraFlexi\RW $abraflexi
     * @param array $properties
     */
    public function __construct($abraflexi, $properties = [])
    {
        $allLabels = \AbraFlexi\Stitek::getAvailbleLabels($abraflexi);
        $enabledLabels = \AbraFlexi\Stitek::getLabels($abraflexi);
        if (empty($enabledLabels)) {
            $enabledLabels = [];
        }
        $enabledLabels = array_flip($enabledLabels);
        if (!isset($properties['id'])) {
            $properties['id'] = $abraflexi->getEvidence() . $abraflexi->getMyKey() . 'labed';
        }
        parent::__construct(null, $properties);
        foreach ($allLabels as $code => $title) {
            $twbsw = $this->addItem(new \Ease\TWB5\Widgets\TWBSwitch(
                $code,
                array_key_exists($code, $enabledLabels),
                1,
                ['onText' => $code, 'offText' => $title, 'onColor' => 'success',
                        'offColor' => 'default', 'labelWidth' => 10, 'handleWidth' => 200
                ]
            ));
            $twbsw->setTagProperties(['data-evidence' => $abraflexi->getEvidence(),
                'data-record' => $abraflexi->getMyKey()]);
        }
    }

    public function finalize()
    {
        \Ease\TWB5\Part::twBootstrapize();
        $this->addJavaScript('

$(\'#' . $this->getTagID() . ' input\').on(\'switchChange.bootstrapSwitch\', function(event, state) {

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
