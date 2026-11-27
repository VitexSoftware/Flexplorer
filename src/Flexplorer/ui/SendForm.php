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
        $this->addInput(new \Ease\Html\TextareaTag(
            'body',
            htmlspecialchars($body, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8'),
            ['id' => 'editor', 'class' => 'query-body-textarea', 'wrap' => 'off'],
        ), _('Query body Text'));
        $propertyPicker = new \Ease\Html\DivTag(null, [
            'class' => 'query-properties mt-3',
            'data-loading' => _('Loading evidence properties...'),
            'data-no-evidence' => _('Enter an evidence URL to select properties.'),
            'data-invalid-body' => _('The request body is not a supported Winstrom document. Fix it before selecting properties or converting formats.'),
            'data-conversion-error' => _('This document cannot be converted without losing data. The original format and body have been retained.'),
            'data-unavailable' => _('Unable to load evidence properties.'),
            'data-no-record' => _('New record'),
        ]);
        $propertyPicker->addItem(new \Ease\Html\H4Tag(_('Evidence properties')));
        $propertyPicker->addItem(new \Ease\Html\DivTag('', ['class' => 'query-properties-message', 'role' => 'status']));
        $propertyPicker->addItem(new \Ease\Html\SelectTag('query-record', [], '', ['class' => 'query-record form-select', 'aria-label' => _('Record')]));
        $propertyPicker->addItem(new \Ease\Html\InputTextTag('query-property-search', '', [
            'class' => 'query-property-search form-control mt-2',
            'placeholder' => _('Search properties...'),
            'aria-label' => _('Search properties'),
            'autocomplete' => 'off',
        ]));
        $propertyPicker->addItem(new \Ease\Html\DivTag(null, ['class' => 'query-property-pills mt-2']));
        $this->addItem($propertyPicker);
        WebPage::singleton()->includeCss('css/query-editor.css');
        WebPage::singleton()->includeJavaScript('js/highlight.min.js');
        WebPage::singleton()->includeJavaScript('js/query-editor.js');
        WebPage::singleton()->includeJavaScript('js/query-properties.js');

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
