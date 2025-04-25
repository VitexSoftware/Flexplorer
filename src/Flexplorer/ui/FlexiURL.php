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
 * Description of FlexiURL.
 *
 * @author vitex
 */
class FlexiURL extends \Ease\Html\DivTag
{
    /**
     * @param array<string, string> $properties
     */
    public function __construct(string $url, $properties = null)
    {
        if (null === $url) {
            $url = WebPage::singleton()->getRequestValue('url');
        }

        if (null === $url) {
            $infoLabel = \constant('ABRAFLEXI_URL').'/c';

            $infoLabel .= '/'.\constant('ABRAFLEXI_COMPANY');

            $evidence = WebPage::singleton()->getRequestValue('evidence');

            if ($evidence) {
                $infoLabel .= '/'.$evidence;
            }
        } else {
            $infoLabel = $url;
        }

        parent::__construct(null, $properties);
        $this->addItem(new \Ease\Html\ATag($infoLabel, urldecode($infoLabel)));
        $id = $this->getTagID();
        WebPage::singleton()->addJavaScript(<<<'EOD'
setInterval(function() {
        $.get("lasturl.php", function (result) {
            $('#
EOD.$id.<<<'EOD'
 a').html(result).attr("href", "query.php?url=" + encodeURI(result.replace('?','%3F').replace('&','%26') ));
        });
    }, 1000);
EOD, null, true);
    }

    public function finalize(): void
    {
        if ($this->finalized === false) {
            parent::finalize();
        }
    }
}
