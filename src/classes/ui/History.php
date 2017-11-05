<?php
/**
 * Flexplorer - Browsing History.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of History
 *
 * @author vitex
 */
class History extends \Ease\Html\Div
{
    public function __construct($content = null, $properties = null)
    {
        if (is_null($properties)) {
            $properties = [];
        }
        $properties['id'] = 'history';

        if (!isset($_SESSION['history'])) {
            $_SESSION['history'] = [];
        }


        parent::__construct(null, $properties);

        foreach (array_reverse($_SESSION['history']) as $bookmark) {
            $this->addItem(new \Ease\Html\Span(new \Ease\Html\ATag($bookmark['url'],
                [new \Ease\TWB\GlyphIcon('bookmark'), ' '.$bookmark['title']]),
                ['class' => 'hitem']));
        }

        $currentUrl = $_SERVER['REQUEST_URI'];
        if (!count($_SESSION['history']) || current(end($_SESSION['history'])) != $currentUrl) {
            $_SESSION['history'][] = ['url' => $currentUrl, 'title' => \Ease\Shared::webPage()->pageTitle];
        }

    }

    function finalize()
    {
        $this->addCss('
            .hitem { background-color: #B5FFC4; margin: 5px; border-radius: 15px 50px 30px 5px; padding-left: 3px; padding-right: 10px; }
            #history { margin: 5px; }
            ');
    }

}
