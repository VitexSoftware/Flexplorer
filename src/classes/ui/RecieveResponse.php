<?php

namespace Flexplorer\ui;

/**
 * Description of RecieveResponse
 *
 * @author vitex
 */
class RecieveResponse extends \Ease\Html\Div
{

    public function finalize()
    {
        $webPage = \Ease\Shared::webPage();
        if ($webPage->isPosted()) {
            $format = 'json';
            $sender = new \FlexiPeeHP\FlexiBeeRW();
            $url    = $webPage->getRequestValue('url');
            $method = $webPage->getRequestValue('method');
            $body   = $webPage->getRequestValue('body');
            if (!is_null($body)) {
                $sender->setPostFields($body);
            }
            $sender->doCurlRequest($url, $method, $format);
            $this->addItem(new \Ease\Html\H1Tag($sender->lastResponseCode));

            if (strlen($sender->lastCurlResponse)) {
                $this->addItem('<pre><code class="'.$format.'">'.
                    $this->jsonpp($sender->lastCurlResponse)
                    .'</code></pre>');
            }

            $webPage->includeCss('css/github.css');
            $webPage->includeJavaScript('js/highlight.min.js');
            $webPage->addJavascript('$(\'pre code\').each(function(i, block) {
    hljs.highlightBlock(block);
  });');
        } else {
            $this->addItem(new \Ease\TWB\Label('info',
                _('Dotaz nebyl zatím odeslán')));
        }
    }

    /**
     * jsonpp - Pretty print JSON data
     *
     * In versions of PHP < 5.4.x, the json_encode() function does not yet provide a
     * pretty-print option. In lieu of forgoing the feature, an additional call can
     * be made to this function, passing in JSON text, and (optionally) a string to
     * be used for indentation.
     *
     * @param string $json  The JSON data, pre-encoded
     * @param string $istr  The indentation string
     *
     * @link https://github.com/ryanuber/projects/blob/master/PHP/JSON/jsonpp.php
     *
     * @return string
     */
    function jsonpp($json, $istr = '  ')
    {
        $result = '';
        for ($p = $q      = $i      = 0; isset($json[$p]); $p++) {
            $json[$p] == '"' && ($p > 0 ? $json[$p - 1] : '') != '\\' && $q = !$q;
            if (!$q && strchr(" \t\n", $json[$p])) {
                continue;
            }
            if (strchr('}]', $json[$p]) && !$q && $i--) {
                strchr('{[', $json[$p - 1]) || $result .= "\n".str_repeat($istr,
                        $i);
            }
            $result .= $json[$p];
            if (strchr(',{[', $json[$p]) && !$q) {
                $i += strchr('{[', $json[$p]) === FALSE ? 0 : 1;
                strchr('}]', $json[$p + 1]) || $result .= "\n".str_repeat($istr,
                        $i);
            }
        }
        return $result;
    }
}