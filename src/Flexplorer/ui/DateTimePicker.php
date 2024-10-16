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
 * Description of Datepicker.
 *
 * @author vitex
 */
class DateTimePicker extends \Ease\Html\DivTag
{
    public $format;

    public function __construct(
        $name,
        $value = null,
        $properties = null,
        $addScript = null
    ) {
        $properties['class'] = 'form-control';

        if (isset($properties['data-format'])) {
            $this->format = $properties['data-format'];
        }

        parent::__construct(new \Ease\Html\InputTextTag(
            $name,
            $value,
            $properties,
        ));
        $this->setTagId($name);
        $this->addItem(new \Ease\Html\Span(
            new \Ease\TWB5\GlyphIcon('calendar'),
            ['class' => 'input-group-addon'],
        ));
        WebPage::singleton()->addJavascript("$('#".$this->getTagID().<<<'EOD'
').datetimepicker({
            locale: "cs",
            format: "
EOD.$this->format.<<<'EOD'
",
            showTodayButton: true,
            showClear: true,
            showClose: true,
            });
EOD);

        if ($addScript) {
            WebPage::singleton()->addJavascript("$('#".$this->getTagID().<<<'EOD'
').datetimepicker().on('dp.change',function(e){

EOD.$addScript.<<<'EOD'
;
}) ;
EOD);
        }
    }

    public function finalize(): void
    {
        WebPage::singleton()->includeCss('css/bootstrap-datetimepicker.min.css');
        WebPage::singleton()->includeJavaScript('js/moment-with-locales.js');
        WebPage::singleton()->includeJavaScript('js/bootstrap-datetimepicker.js');
        $this->setTagClass('input-group datetime');
        parent::finalize();
    }
}
