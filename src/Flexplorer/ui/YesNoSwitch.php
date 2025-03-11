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
 * Description of YesNoSwitch.
 *
 * @author vitex
 */
class YesNoSwitch extends \Ease\TWB5\Widgets\TWBSwitch
{
    public $keyCode = 'var key = $(".keyId").val();';

    public function __construct(
        $name,
        $checked = false,
        $value = null,
        $properties = null,
    ) {
        parent::__construct($name, $checked, 'on', $properties);
    }

    public function finalize(): void
    {
        parent::finalize();
        $this->addJavascript('$("[name=\''.$this->getTagName().<<<'EOD'
']").on('switchChange.bootstrapSwitch', function(event, state) {

        var saverClass = $("[name='class']").val();
        var evidence = $("[name='evidence']").val();

EOD.$this->keyCode.<<<'EOD'


        if(key) {
            var field = $(this).attr("name");
            var input = $("[name='
EOD.$this->getTagName().<<<'EOD'
']");

            $.post('datasaver.php', {
                SaverClass: saverClass,
                Field: field,
                Evidence: evidence,
                Value: state,
                Key: key,
                success: function () {
                    input.parent().parent().css({borderColor: "#0f0", borderStyle: "solid"}).animate({borderWidth: '5px'}, 'slow', 'linear');
                    input.parent().parent().animate({borderColor: 'gray', borderWidth: '1px'});
                }
            }
            ).fail(function () {
                    input.parent().parent().css({borderColor: "#f00", borderStyle: "solid"}).animate({borderWidth: '5px'}, 'slow', 'linear');
                    input.parent().parent().animate({borderColor: 'gray', borderWidth: '1px'});
            });
        }

        });

EOD, null, true);
    }
}
