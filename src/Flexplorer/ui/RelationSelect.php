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

class RelationSelect extends \Ease\Html\InputTextTag
{
    public function finalize(): void
    {
        $this->setTagID('AdresarSelect');
        WebPage::singleton()->includeJavaScript('js/handlebars.js');
        WebPage::singleton()->includeJavaScript('js/typeahead.bundle.js');

        WebPage::singleton()->addJavaScript(<<<'EOD'



var addresses = new Bloodhound({
    limit: 1000,
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: 'searcher.php?evidence=
EOD.$this->getTagProperty('data-evidence').<<<'EOD'
&q=%QUERY',
      wildcard: '%QUERY'
    }
});

addresses.initialize();

$('input[name="
EOD.$this->getTagName().<<<'EOD'
"]').typeahead(null, {
    displayKey: 'name',
    limit: 1000,
    minLength: 3,
    highlight: true,
    select: function( event,suggest ) { alert( suggest ) },
    source: addresses.ttAdapter(),
     templates: {
        suggestion: Handlebars.compile('<p><small>{{type}} #<span class="idkey">{{id}}</span></small><br><strong>{{name}}</strong> – {{what}}</p>')
    }
});

$(".twitter-typeahead").css("display","block");

EOD, null, true);
    }
}
