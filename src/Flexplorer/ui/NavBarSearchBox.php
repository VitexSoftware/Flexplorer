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

class NavBarSearchBox extends \Ease\Html\Form
{
    /**
     * Formulář Bootstrapu.
     *
     * @param string     $formName      jméno formuláře
     * @param string     $formAction    cíl formulář např login.php
     * @param null|mixed $term
     * @param array      $tagProperties vlastnosti tagu například:
     *                                  array('enctype' => 'multipart/form-data')
     */
    public function __construct(
        $formName,
        $formAction = null,
        $term = null,
        $tagProperties = []
    ) {
        parent::__construct(array_merge(['name' => $formName, 'action' => $formAction, 'method' => 'post'], $tagProperties));

        $this->setTagProperties(['class' => 'navbar-form', 'role' => 'search']);
        $group = $this->addItem(
            new \Ease\Html\DivTag(new \Ease\Html\InputTextTag(
                'search',
                $term,
                [
                    'class' => 'form-control pull-right typeahead input-sm',
                    'style' => 'width: 200px; margin-right: 35px, border: 1px solid black; background-color: #e5e5e5; height: 27px',
                    'placeholder' => _('Search'),
                ],
            ), ['class' => 'input-group']),
        );
        $buttons = $group->addItem(new \Ease\Html\SpanTag(
            null,
            ['class' => 'input-group-btn'],
        ));
        $buttons->addItem(new \Ease\Html\ButtonTag(
            new \Ease\Html\SpanTag(
                new \Ease\Html\SpanTag(_('Close'), ['class' => 'sr-only']),
                ['class' => 'glyphicon glyphicon-remove'],
            ),
            ['type' => 'reset', 'class' => 'btn btn-default btn-sm'],
        ));
        $buttons->addItem(new \Ease\Html\ButtonTag(
            new \Ease\Html\SpanTag(
                new \Ease\Html\SpanTag(_('Search'), ['class' => 'sr-only']),
                ['class' => 'glyphicon glyphicon-search'],
            ),
            ['type' => 'submit', 'class' => 'btn btn-default btn-sm '],
        ));
    }

    public function finalize(): void
    {
        WebPage::singleton()->includeJavaScript('js/handlebars.js');
        WebPage::singleton()->includeJavaScript('js/typeahead.bundle.js');
        WebPage::singleton()->addJavaScript(<<<'EOD'

var bestPictures = new Bloodhound({
    limit: 1000,
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: 'searcher.php?q=%QUERY',
      wildcard: '%QUERY'
    }
});

bestPictures.initialize();

$('input[name="search"]').typeahead(null, {
    name: 'best-pictures',
    displayKey: 'name',
    limit: 1000,
    minLength: 3,
    highlight: true,
    source: bestPictures.ttAdapter(),
     templates: {
        suggestion: Handlebars.compile('<p><small>{{type}}</small><br><a href="{{url}}"><strong>{{name}}</strong> – {{what}}</a></p>')
}
});


EOD, null, true);
        parent::finalize();
    }
}
