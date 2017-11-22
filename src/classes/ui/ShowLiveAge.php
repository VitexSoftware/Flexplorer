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
        $this->setTagID();

        $this->addJavaScript('
var timestamp'.$this->getTagID().' = '.$age.';

function component(x, v) {
    return Math.floor(x / v);
}

var $div'.$this->getTagID().' = $(\'#'.$this->getTagID().'\');

setInterval(function() {

    timestamp'.$this->getTagID().'++;

    var days'.$this->getTagID().'    = component(timestamp'.$this->getTagID().', 24 * 60 * 60),
        hours'.$this->getTagID().'   = component(timestamp'.$this->getTagID().',      60 * 60) % 24,
        minutes'.$this->getTagID().' = component(timestamp'.$this->getTagID().',           60) % 60,
        seconds'.$this->getTagID().' = component(timestamp'.$this->getTagID().',            1) % 60;

    $div'.$this->getTagID().'.html(days'.$this->getTagID().' + " '._('days').', " + hours'.$this->getTagID().' + ":" + minutes'.$this->getTagID().' + ":" + seconds'.$this->getTagID().');

}, 1000);
            ');
    }
}
