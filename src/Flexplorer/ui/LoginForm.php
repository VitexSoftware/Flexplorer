<?php

/**
 * AbraFlexi Bricks - Connection Config Form
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace Flexplorer\ui;

use Ease\TWB\Form;
use Ease\Html\InputTextTag;

/**
 * Form to configure used Abraflexi instance
 *
 * @author vitex
 */
class LoginForm extends Form {

    /**
     * Abraflexi URL Input name
     * @var string eg. https://demo.abraflexi.eu:5434
     */
    public $urlField = 'server';

    /**
     * Abraflexi User Input name
     * @var string eg. winstrom
     */
    public $usernameField = 'login';

    /**
     * Abraflexi Password Input name
     * @var string eg. winstrom
     */
    public $passwordField = 'password';

    /**
     * Abraflexi Company Input name
     * @var string eg. demo_s_r_o_
     */
    public $companyField = 'company';

    /**
     * Abraflexi Server connection form
     * 
     * @param array $options           ConnectionOptions options
     * @param array $formProperties    FormTag properties eg. ['enctype' => 'multipart/form-data']
     * @param mixed $formContents      Any other initial content
     */
    public function __construct(array $options,  array $formProperties = [], $formContents = null) {
        parent::__construct($formProperties, $formContents);

        $this->addInput(new InputTextTag($this->urlField),
                _('RestAPI endpoint url'));

        $this->addInput(new InputTextTag($this->usernameField),
                _('REST API Username'));

        $this->addInput(new InputTextTag($this->passwordField),
                _('Rest API Password'));
 
        $this->addItem( new \Ease\TWB\SubmitButton(_('Sign in')) );
        
        $this->fillUp($options);
    }
}
