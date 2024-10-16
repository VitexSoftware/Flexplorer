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

        $this->addItem(new \Ease\Html\InputHiddenTag('evidence', $evidence));

        $this->addInput(
            new \Ease\Html\InputTextTag(
                'title',
                _('Open in Flexplorer'),
                ['id' => 'buttonTitle'],
            ),
            _('Button Title'),
        );

        $this->addInput(new \Ease\Html\InputTextTag(
            'kod',
            null,
            ['id' => 'buttonCode', 'maxlength' => 20],
        ), _('Code'));

        $this->addInput(new \Ease\Html\InputTextTag(
            'description',
            null,
            ['id' => 'buttonDescription'],
        ), _('Button Description'));

        $this->addInput(
            new \Ease\Html\InputUrlTag('url', $url),
            _('Button target Url'),
            $url,
            new \Ease\Html\ATag(
                'https://www.flexibee.eu/api/dokumentace/ref/uzivatelske-tlacitko/',
                _('Api Documentation'),
            ),
        );

        $this->addInput(
            new \Ease\Html\SelectTag(
                'location',
                ['list' => _('List'), 'detail' => _('Detail')],
            ),
            _('Button Location in AbraFlexi'),
        );

        $this->addInput(
            new \Ease\Html\SelectTag(
                'browser',
                ['automatic' => _('Automatic'), 'desktop' => _('Desktop')],
            ),
            _('Browser used'),
        );

        $this->addItem(new \Ease\TWB5\SubmitButton(_('Save New Button'), 'success'));
    }

    public function finalize(): void
    {
        $this->addJavaScript(<<<'EOD'
$('#buttonTitle').change(function() {
         if($.trim($('#buttonCode').val()) == ''){
            $('#buttonCode').val($(this).val().toUpperCase().replace(/\s/g,"_").substring(0,20));
         }
         if($.trim($('#buttonDescription').val()) == ''){
            $('#buttonDescription').val($(this).val());
         }

});
EOD);
        parent::finalize();
    }
}
