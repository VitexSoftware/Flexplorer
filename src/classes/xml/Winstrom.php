<?php

namespace Flexplorer\xml;

/**
 * Description of Winstrom
 *
 * @author vitex
 */
class Winstrom extends \Ease\Html\PairTag
{
    public function __construct($content = null, $tagProperties = [])
    {
        $tagProperties['version'] = '1.0';
        parent::__construct('winstrom', $tagProperties, $content);
    }
}
