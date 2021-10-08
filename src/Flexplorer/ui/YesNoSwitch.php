<?php

/**
 * Yes/No switch
 *
 * @package   Flexplorer
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2018 VitexSoftware
 */

namespace Flexplorer\ui;

/**
 * Description of YesNoSwitch
 *
 * @author vitex
 */
class YesNoSwitch extends \Ease\TWB\Widgets\TWBSwitch {

    public $keyCode = 'var key = $(".keyId").val();';

    function __construct($name, $checked = false, $value = null,
            $properties = null) {
        parent::__construct($name, $checked, 'on', $properties);
    }

    function finalize() {
        parent::finalize();
        $this->addJavascript('$("[name=\'' . $this->getTagName() . '\']").on(\'switchChange.bootstrapSwitch\', function(event, state) {

        var saverClass = $("[name=\'class\']").val();
        var evidence = $("[name=\'evidence\']").val();
        ' . $this->keyCode . '

        if(key) {
            var field = $(this).attr("name");
            var input = $("[name=\'' . $this->getTagName() . '\']");

            $.post(\'datasaver.php\', {
                SaverClass: saverClass,
                Field: field,
                Evidence: evidence,
                Value: state,
                Key: key,
                success: function () {
                    input.parent().parent().css({borderColor: "#0f0", borderStyle: "solid"}).animate({borderWidth: \'5px\'}, \'slow\', \'linear\');
                    input.parent().parent().animate({borderColor: \'gray\', borderWidth: \'1px\'});
                }
            }
            ).fail(function () {
                    input.parent().parent().css({borderColor: "#f00", borderStyle: "solid"}).animate({borderWidth: \'5px\'}, \'slow\', \'linear\');
                    input.parent().parent().animate({borderColor: \'gray\', borderWidth: \'1px\'});
            });
        }

        });
            ', null, true);
    }

}
