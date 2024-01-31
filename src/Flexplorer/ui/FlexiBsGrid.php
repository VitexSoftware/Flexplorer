<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of FlexiBsGrid
 *
 * @author vitex
 */
class FlexiBsGrid extends BsGrid
{
    /**
     * Zdroj dat
     * @var \Flexplorer\Flexplorer
     */
    public $dataSource = null;

    public function __construct($dataSource, $properties = null)
    {
        parent::__construct($dataSource->getEvidence(), $properties);
    }
}
