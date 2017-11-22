<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of ShowLiveAge
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class ShowLiveAge extends \Ease\Html\TimeTag
{
    public function __construct($timestamp, $properties = [])
    {
        $age = time() - $timestamp;
        $days = floor($age / 86400);
        parent::__construct($days.' '._('days').', '.gmdate("G:i:s", $age),
            $properties);
        $this->setTagID('timestamp');

        $this->addJavaScript('
var timestamp = '.$age.';

function component(x, v) {
    return Math.floor(x / v);
}

var $div = $(\'#'.$this->getTagID().'\');

setInterval(function() {

    timestamp++;

    var days    = component(timestamp, 24 * 60 * 60),
        hours   = component(timestamp,      60 * 60) % 24,
        minutes = component(timestamp,           60) % 60,
        seconds = component(timestamp,            1) % 60;

    $div.html(days + " '._('days').', " + hours + ":" + minutes + ":" + seconds);

}, 1000);
            ');
    }
}