<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of ButtonForm
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class ButtonForm extends \Ease\TWB\Form {

    /**
     * Form for new Custom Button in given evidence
     * 
     * @param string $evidence evidence dbNazev
     */
    public function __construct($evidence) {
        parent::__construct(['name' => 'button', 'action' => 'editor.php?evidence=custom-button', 'method' => 'POST']);
        $url = dirname((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $url .= '/editor.php?evidence=' . $evidence . '&id=${object.id}';

        $this->addItem(new \Ease\Html\InputHiddenTag('evidence', $evidence));

        $this->addInput(new \Ease\Html\InputTextTag('title',
                        _('Open in Flexplorer'), ['id' => 'buttonTitle']),
                _('Button Title'));

        $this->addInput(new \Ease\Html\InputTextTag('kod', null,
                        ['id' => 'buttonCode', 'maxlength' => 20]), _('Code'));

        $this->addInput(new \Ease\Html\InputTextTag('description', null,
                        ['id' => 'buttonDescription']), _('Button Description'));

        $this->addInput(new \Ease\Html\InputUrlTag('url', $url),
                _('Button target Url'), $url,
                new \Ease\Html\ATag('https://www.flexibee.eu/api/dokumentace/ref/uzivatelske-tlacitko/',
                        _('Api Documentation')));

        $this->addInput(new \Ease\Html\SelectTag('location',
                        ['list' => _('List'), 'detail' => _('Detail')]),
                _('Button Location in AbraFlexi'));

        $this->addInput(new \Ease\Html\SelectTag('browser',
                        ['automatic' => _('Automatic'), 'desktop' => _('Desktop')]),
                _('Browser used'));

        $this->addInput(new \Ease\TWB\SubmitButton(_('Save New Button'),
                        'success'));
    }

    /**
     * 
     */
    public function finalize() {
        $this->addJavaScript('$(\'#buttonTitle\').change(function() {
         if($.trim($(\'#buttonCode\').val()) == \'\'){
            $(\'#buttonCode\').val($(this).val().toUpperCase().replace(/\s/g,"_").substring(0,20));
         }
         if($.trim($(\'#buttonDescription\').val()) == \'\'){
            $(\'#buttonDescription\').val($(this).val());
         }
        
});   ');
        parent::finalize();
    }

}
