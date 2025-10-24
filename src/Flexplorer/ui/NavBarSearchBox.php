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
     * Bootstrap 5 Search Form for Navbar.
     *
     * @param string     $formName      form name
     * @param string     $formAction    form target
     * @param null|mixed $term          initial search term
     * @param array      $tagProperties additional tag properties
     */
    public function __construct(
        $formName,
        $formAction = null,
        $term = null,
        $tagProperties = [],
    ) {
        parent::__construct(array_merge(['name' => $formName, 'action' => $formAction, 'method' => 'post'], $tagProperties));

        $this->setTagProperties(['class' => 'd-flex align-items-center ms-3', 'role' => 'search']);
        
        // Add typeahead input with Bootstrap 5 styling
        $this->addItem(new \Ease\Html\InputTextTag(
            'q',
            $term,
            [
                'class' => 'form-control me-2 typeahead',
                'type' => 'search',
                'placeholder' => _('Search'),
                'aria-label' => _('Search'),
                'autocomplete' => 'off',
                'style' => 'width: 250px;',
            ],
        ));
        
        // Add submit button
        $this->addItem(new \Ease\Html\ButtonTag(
            _('Search'),
            ['type' => 'submit', 'class' => 'btn btn-outline-light'],
        ));
    }

    public function finalize(): void
    {
        WebPage::singleton()->includeJavaScript('js/handlebars.js');
        WebPage::singleton()->includeJavaScript('js/typeahead.bundle.js');
        WebPage::singleton()->addJavaScript(<<<'EOD'

var searchEngine = new Bloodhound({
    limit: 1000,
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: 'searcher.php?q=%QUERY',
        wildcard: '%QUERY'
    }
});

$('input[name="q"]').typeahead({
    minLength: 3,
    highlight: true,
    hint: false
}, {
    name: 'flexplorer-search',
    display: 'name',
    limit: 1000,
    source: searchEngine,
    templates: {
        suggestion: Handlebars.compile('<div><small class="text-muted">{{type}}</small><br><strong>{{name}}</strong> <span class="text-muted">– {{what}}</span></div>')
    }
}).on('typeahead:select', function(evt, item) {
    window.location.href = item.url;
});

EOD, null, true);
        parent::finalize();
    }
}
