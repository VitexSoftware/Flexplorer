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

namespace Flexplorer\xml;

/**
 * Description of CustomButton.
 *
 * @author vitex
 */
class FelexiBeeButtonXML extends CustomButton
{
    public function __construct($code, $url, $title, $desc, $evidence, $location, $browser)
    {
        parent::__construct($this->tagType, null, [
            new IdTag(\AbraFlexi\Functions::code($code)),
            new UrlTag($url),
            new \Ease\Html\TitleTag($title),
            new DescriptionTag($desc),
            new EvidenceTag($evidence),
            new LocationTag($location),
            new BrowserTag($browser),
        ]);
    }

    /**
     * Create a stable button code within the ABRA Flexi 20-character limit.
     */
    public static function buttonCode(string $evidence, string $kind): string
    {
        $code = strtoupper(str_replace('-', '', $evidence).$kind);

        return \strlen($code) <= 20 ? $code : substr($code, 0, 11).'-'.strtoupper(substr(hash('sha256', $code), 0, 8));
    }
}
