<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexplorer\ui;

/**
 * Description of FlexiURL
 *
 * @author vitex
 */
class FlexiURL extends \Ease\Html\DivTag
{
    /**
     *
     * @param type $url
     * @param type $properties
     */
    public function __construct($url = null, $properties = null)
    {
        if (is_null($url)) {
            $url = WebPage::singleton()->getRequestValue('url');
        }
        if (is_null($url)) {
            $infoLabel = constant('ABRAFLEXI_URL') . '/c';

            $infoLabel .= '/' . constant('ABRAFLEXI_COMPANY');

            $evidence = WebPage::singleton()->getRequestValue('evidence');
            if ($evidence) {
                $infoLabel .= '/' . $evidence;
            }
        } else {
            $infoLabel = $url;
        }

        parent::__construct(null, $properties);
        $this->addItem(new \Ease\Html\ATag($infoLabel, urldecode($infoLabel)));
        $id = $this->getTagID();
        WebPage::singleton()->addJavaScript("setInterval(function() {
        $.get(\"lasturl.php\", function (result) {
            $('#" . $id . " a').html(result).attr(\"href\", \"query.php?url=\" + encodeURI(result.replace('?','%3F').replace('&','%26') ));
        });
    }, 1000);", null, true);
    }
}
