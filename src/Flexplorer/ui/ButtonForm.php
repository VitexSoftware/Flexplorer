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
 * Description of ButtonForm.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class ButtonForm extends \Ease\TWB5\Form
{
    /**
     * Form for new Custom Button in given evidence.
     *
     * @param string $evidence evidence dbNazev
     */
    public function __construct($evidence)
    {
        parent::__construct(['name' => 'button', 'action' => 'editor.php?evidence=custom-button', 'method' => 'POST']);
        $url = \dirname((isset($_SERVER['HTTPS']) ? 'https' : 'http')."://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
        $url .= '/editor.php?evidence='.$evidence.'&id=${object.id}';
        $urlHelp = _('Use an absolute URL. The file:// protocol is not supported. FreeMarker variables such as ${object.ic} and ${objectIds} can be used; do not use both in the same URL.')
            .' '.(string) new \Ease\Html\ATag(
                _('Custom button documentation URL'),
                _('Supported variables and button definition'),
            );

        $this->addItem(new \Ease\Html\InputHiddenTag('evidence', $evidence));

        $this->addInput(
            new \Ease\Html\InputTextTag(
                'title',
                _('Open in Flexplorer'),
                ['id' => 'buttonTitle', 'required' => 'required'],
            ),
            _('Button Title'),
        );

        $this->addInput(new \Ease\Html\InputTextTag(
            'kod',
            null,
            ['id' => 'buttonCode', 'maxlength' => 20, 'required' => 'required'],
        ), _('Code'));

        $this->addInput(new \Ease\Html\InputTextTag(
            'description',
            null,
            ['id' => 'buttonDescription', 'required' => 'required'],
        ), _('Button Description'));

        $this->addInput(
            new \Ease\Html\InputTextTag('url', $url, ['required' => 'required']),
            _('Button target Url'),
            $url,
            $urlHelp,
        );

        $this->addInput(
            new \Ease\Html\SelectTag(
                'location',
                ['list' => _('List'), 'detail' => _('Detail')],
                'list',
            ),
            _('Button Location in AbraFlexi'),
            null,
            _('Create a separate button definition for each location where it should appear.'),
        );

        $this->addInput(
            new \Ease\Html\SelectTag(
                'browser',
                ['automatic' => _('Automatic'), 'desktop' => _('Desktop')],
                'automatic',
            ),
            _('Browser used'),
            null,
            _('The browser setting is ignored in the web interface.'),
        );

        $this->addItem(new \Ease\TWB5\SubmitButton(_('Save New Button'), 'success'));
    }

    public function finalize(): void
    {
        $this->addJavaScript(<<<'EOD'
function fillButtonDefaults() {
         const title = $('#buttonTitle').val();
         if($.trim($('#buttonCode').val()) == ''){
            $('#buttonCode').val(title.toUpperCase().replace(/\s/g,"_").substring(0,20));
         }
         if($.trim($('#buttonDescription').val()) == ''){
            $('#buttonDescription').val(title);
         }
}
$('#buttonTitle').on('change', fillButtonDefaults);
fillButtonDefaults();
EOD);
        parent::finalize();
    }
}
