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
 * Description of SendForm.
 *
 * @author vitex
 */
class SendForm extends \Ease\TWB5\Form
{
    /**
     * Formulář pro odeslání uživatelského požadavku.
     *
     * @param mixed $format
     */
    public function __construct(
        string $url,
        string $method = 'GET',
        string $body = '',
        string $format = 'json',
    ) {
        $sourceurl = '';
        parent::__construct(['name' => 'Req', 'action' => 'query.php', 'method' => 'POST', 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data']);

        $this->addInput(
            new \Ease\Html\InputTextTag('url', $url),
            _('URL'),
            $url,
            new \Ease\Html\ATag(
                'https://www.flexibee.eu/api/dokumentace/ref/urls',
                _('URL Compositon'),
            ),
        );
        $this->addInput(new JsonTextarea(
            'body',
            $body,
            ['id' => 'editor', 'class' => 'animated'],
        ), _('Query body Text'));

        $this->addInput(
            new \Ease\Html\InputFileTag('upload'),
            _('Query body from File'),
            'data...json.xml.csv',
            _('This file overwrite "request body"'),
        );

        $this->addInput(
            new \Ease\Html\InputTextTag('sourceurl', $sourceurl),
            _('Source URL'),
            $sourceurl,
            _('Get Query Body from url'),
        );

        $this->addInput(
            new \Ease\Html\SelectTag(
                'method',
                ['GET' => 'GET', 'POST' => 'POST', 'PUT' => 'PUT', 'PATCH' => 'PATCH',
                    'DELETE' => 'DELETE'],
                $method,
            ),
            _('Method'),
            null,
            new \Ease\Html\ATag(
                'https://www.flexibee.eu/api/dokumentace/ref/http-operations',
                _('Supported HTTP Operations'),
            ),
        );

        $this->addInput(
            new \Ease\Html\SelectTag(
                'format',
                ['json' => 'JSON', 'xml' => 'XML', 'csv' => 'CSV'],
                $format,
            ),
            _('Format'),
        );

        $this->addItem(new \Ease\TWB5\SubmitButton(_('Send'), 'success'));
    }
}
