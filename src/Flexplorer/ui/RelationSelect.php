<?php

/**
 * Flexplorer - vršek stránky.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

class RelationSelect extends \Ease\Html\InputTextTag {

    public function finalize() {
        $this->setTagID('AdresarSelect');
        WebPage::singleton()->includeJavaScript('js/handlebars.js');
        WebPage::singleton()->includeJavaScript('js/typeahead.bundle.js');

        WebPage::singleton()->addJavaScript('


var addresses = new Bloodhound({
    limit: 1000,
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace(\'value\'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: \'searcher.php?evidence=' . $this->getTagProperty('data-evidence') . '&q=%QUERY\',
      wildcard: \'%QUERY\'
    }
});

addresses.initialize();

$(\'input[name="' . $this->getTagName() . '"]\').typeahead(null, {
    displayKey: \'name\',
    limit: 1000,
    minLength: 3,
    highlight: true,
    select: function( event,suggest ) { alert( suggest ) },
    source: addresses.ttAdapter(),
     templates: {
        suggestion: Handlebars.compile(\'<p><small>{{type}} #<span class="idkey">{{id}}</span></small><br><strong>{{name}}</strong> – {{what}}</p>\')
    }
});

$(".twitter-typeahead").css("display","block");
', null, true);
    }

}
