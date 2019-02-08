<?php
/**
 * Flexplorer - Vyhledávací políčko.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class NavBarSearchBox extends \Ease\TWB\Form
{

    /**
     * Formulář Bootstrapu.
     *
     * @param string $formName      jméno formuláře
     * @param string $formAction    cíl formulář např login.php
     * @param string $formMethod    metoda odesílání POST|GET
     * @param mixed  $formContents  prvky uvnitř formuláře
     * @param array  $tagProperties vlastnosti tagu například:
     *                              array('enctype' => 'multipart/form-data')
     */
    public function __construct($formName, $formAction = null,
                                $term = null,
                                $tagProperties = null)
    {
        parent::__construct($formName, $formAction, 'post', null, $tagProperties);

        $this->setTagProperties(['class' => 'navbar-form', 'role' => 'search']);
        $group   = $this->addItem(
            new \Ease\Html\DivTag(new \Ease\Html\InputTextTag(
            'search', $term,
            [
            'class' => 'form-control pull-right typeahead input-sm',
            'style' => 'width: 200px; margin-right: 35px, border: 1px solid black; background-color: #e5e5e5; height: 27px',
            'placeholder' => _('Search'),
            ]), ['class' => 'input-group'])
        );
        $buttons = $group->addItem(new \Ease\Html\SpanTag( null,
            ['class' => 'input-group-btn']));
        $buttons->addItem(new \Ease\Html\ButtonTag(new \Ease\Html\SpanTag(
            new \Ease\Html\SpanTag( _('Close'), ['class' => 'sr-only']),
            ['class' => 'glyphicon glyphicon-remove']),
            ['type' => 'reset', 'class' => 'btn btn-default btn-sm']));
        $buttons->addItem(new \Ease\Html\ButtonTag(new \Ease\Html\SpanTag(
            new \Ease\Html\SpanTag( _('Search'), ['class' => 'sr-only']),
            ['class' => 'glyphicon glyphicon-search']),
            ['type' => 'submit', 'class' => 'btn btn-default btn-sm ']));
    }

    public function finalize()
    {
        \Ease\Shared::webPage()->includeJavaScript('js/handlebars.js');
        \Ease\Shared::webPage()->includeJavaScript('js/typeahead.bundle.js');

        /*
          \Ease\Shared::webPage()->addCss('

          .tt-hint {
          }

          .tt-input {
          }

          .tt-hint {
          color: #999
          }

          .tt-dropdown-menu {
          width: 422px;
          margin-top: 12px;
          padding: 8px 0;
          background-color: #fff;
          border: 1px solid #ccc;
          border: 1px solid rgba(0, 0, 0, 0.2);
          border-radius: 8px;
          box-shadow: 0 5px 10px rgba(0,0,0,.2);
          overflow-y: auto;
          max-height: 500px;
          }

          .tt-suggestion {
          padding: 3px 20px;
          }

          .tt-suggestion.tt-cursor {
          color: #fff;
          background-color: #0097cf;

          }

          .tt-suggestion.tt-cursor a {
          color: black;
          }

          .tt-suggestion p {
          margin: 0;
          }
          ');

         */
        \Ease\Shared::webPage()->addJavaScript('


var bestPictures = new Bloodhound({
    limit: 1000,
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace(\'value\'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: \'searcher.php?q=%QUERY\',
      wildcard: \'%QUERY\'
    }
});

bestPictures.initialize();

$(\'input[name="search"]\').typeahead(null, {
    name: \'best-pictures\',
    displayKey: \'name\',
    limit: 1000,
    minLength: 3,
    highlight: true,
    source: bestPictures.ttAdapter(),
     templates: {
        suggestion: Handlebars.compile(\'<p><small>{{type}}</small><br><a href="{{url}}"><strong>{{name}}</strong> – {{what}}</a></p>\')
}
});

            ', null, true);
    }
}