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

use Ease\Html\InputTextTag;
use Ease\TWB5\Form;

/**
 * Form to configure used Abraflexi instance.
 *
 * @author vitex
 */
class LoginForm extends Form
{
    /**
     * Abraflexi URL Input name.
     *
     * @var string eg. https://demo.abraflexi.eu:5434
     */
    public string $urlField = 'server';

    /**
     * Abraflexi User Input name.
     *
     * @var string eg. winstrom
     */
    public string $usernameField = 'login';

    /**
     * Abraflexi Password Input name.
     *
     * @var string eg. winstrom
     */
    public string $passwordField = 'password';

    /**
     * Abraflexi Company Input name.
     *
     * @var string eg. demo_s_r_o_
     */
    public string $companyField = 'company';

    /**
     * Abraflexi Server connection form.
     *
     * @param array $options        ConnectionOptions options
     * @param array $formProperties FormTag properties eg. ['enctype' => 'multipart/form-data']
     * @param mixed $formContents   Any other initial content
     */
    public function __construct(array $options, array $formProperties = [], $formContents = null)
    {
        parent::__construct($formProperties, $formContents);

        $this->addInput(
            new InputTextTag($this->urlField),
            _('RestAPI endpoint url'),
        );

        $this->addInput(
            new InputTextTag($this->usernameField),
            _('REST API Username'),
        );

        $this->addInput(
            new InputTextTag($this->passwordField),
            _('Rest API Password'),
        );

        $this->addItem(new \Ease\TWB5\SubmitButton(_('Sign in')));

        $this->fillUp($options);
    }
}
