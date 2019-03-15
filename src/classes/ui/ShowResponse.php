<?php

namespace Flexplorer\ui;

/**
 * Description of RecieveResponse
 *
 * @author vitex
 */
class ShowResponse extends \Ease\Html\DivTag
{
    /**
     * Class for FlexiBee interaction
     * @var \FlexiPeeHP\FlexiBeeRW
     */
    public $sender = null;

    /**
     * Recieve FlexiBee reuest response
     *
     * @param string $url
     * @param \FlexiPeeHP\FlexiBeeRW $engine custom engine for request performing
     */
    public function __construct($engine)
    {
        $this->sender = $engine;
        $this->setTagClass('ResponseShow');
        parent::__construct();
    }

    public function finalize()
    {
        $formatedResponse = '';
        /* @var $webPage \Ease\TWB\WebPage */
        $webPage          = \Ease\Shared::webPage();

        $this->addItem(new \Ease\Html\H1Tag($this->sender->lastResponseCode.': '.self::responseCodeMessage($this->sender->lastResponseCode)));

        if (strlen($this->sender->lastCurlResponse)) {
            $format = $this->sender->getResponseFormat();
            switch ($format) {
                case 'application/json':
                case 'json':
                    $format           = 'json';
                    $formated         = self::jsonpp($this->sender->lastCurlResponse);
                    $formatedResponse = $formated;
                    $formated         = preg_replace('/ref":"(.*)"/',
                        'ref":"<a href="query.php?show=result&url='.$this->sender->url.'$1">$1</a>"',
                        $formated);
                    $formated         = preg_replace('/@evidence":"(.*)"/',
                        '@evidence":"<a href="evidence.php?evidence=$1">$1</a>"',
                        $formated);
                    break;
                case 'application/xml':
                case 'xml':
                    $format           = 'xml';
                    $formatedResponse = $this->sender->lastCurlResponse;
                    $formated         = htmlentities($this->sender->lastCurlResponse);
                    $formated         = preg_replace('/ref=&quot;(.*)&quot;/',
                        'ref=&quot;<a href="query.php?show=result&url='.$this->sender->url.'$1">$1</a>&quot;',
                        $formated);
                    $formated         = preg_replace('/evidence=&quot;(.*)&quot;/',
                        'evidence=&quot;<a href="evidence.php?evidence=$1">$1</a>&quot;',
                        $formated);
                    break;
                case 'txt':
                default :
                    $formated         = $this->sender->lastCurlResponse;
                    break;
            }

            $formated = $this->addEvidenciesLinks( $formated );
            
            $this->addItem('<pre><code class="'.$format.'">'.
                $formated
                .'</code></pre>');
            if (strlen($formatedResponse)) {
                $this->addJavaScript('
                        function responseToRequest() {
                            $("#editor").val( $("#formatedResponse").html() );
                            $("#Request a:first").tab("show");
                            $("#editor").focus();
                            $("#editor").change();
                        };

function downloadResponse(){
    var a = document.body.appendChild(
        document.createElement("a")
    );
    a.download = "'.\Ease\Sand::lettersOnly($this->sender->curlInfo['url']).'.'.$format.'";
    a.href = "data:'.$this->sender->curlInfo['content_type'].'," + document.getElementById("formatedResponse").innerHTML; // Grab the HTML
    a.click(); // Trigger a click on the element
}
                        ', null, false);
                $this->addItem(new \Ease\Html\DivTag($formatedResponse,
                    ['id' => 'formatedResponse', 'style' => 'visibility: hidden; height: 0px;']));
                $this->addItem(new \Ease\TWB\LinkButton('#',
                    _('Make new request from this response').new \Ease\TWB\GlyphIcon('repeat'),
                    'success', ['onClick' => 'responseToRequest();']));

                $this->addItem(new \Ease\TWB\LinkButton('#',
                    _('Save this response to file').new \Ease\TWB\GlyphIcon('floppy-save'),
                    'success', ['onClick' => 'downloadResponse();']));
            }
        }

        $webPage->includeCss('css/github.css');
        $webPage->includeJavaScript('js/highlight.min.js');
        $webPage->addJavascript('$(\'pre code\').each(function(i, block) {
    hljs.highlightBlock(block);
  });');
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
    static public function jsonpp($json, $istr = '  ')
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

    /**
     * Obtain status code message for given code
     *
     * @param int $code ex. 200|500|...
     * @return string code message
     */
    static public function responseCodeMessage($code)
    {
        switch ($code) {
            case 100: $text = 'Continue';
                break;
            case 101: $text = 'Switching Protocols';
                break;
            case 200: $text = 'OK';
                break;
            case 201: $text = 'Created';
                break;
            case 202: $text = 'Accepted';
                break;
            case 203: $text = 'Non-Authoritative Information';
                break;
            case 204: $text = 'No Content';
                break;
            case 205: $text = 'Reset Content';
                break;
            case 206: $text = 'Partial Content';
                break;
            case 300: $text = 'Multiple Choices';
                break;
            case 301: $text = 'Moved Permanently';
                break;
            case 302: $text = 'Moved Temporarily';
                break;
            case 303: $text = 'See Other';
                break;
            case 304: $text = 'Not Modified';
                break;
            case 305: $text = 'Use Proxy';
                break;
            case 400: $text = 'Bad Request';
                break;
            case 401: $text = 'Unauthorized';
                break;
            case 402: $text = 'Payment Required';
                break;
            case 403: $text = 'Forbidden';
                break;
            case 404: $text = 'Not Found';
                break;
            case 405: $text = 'Method Not Allowed';
                break;
            case 406: $text = 'Not Acceptable';
                break;
            case 407: $text = 'Proxy Authentication Required';
                break;
            case 408: $text = 'Request Time-out';
                break;
            case 409: $text = 'Conflict';
                break;
            case 410: $text = 'Gone';
                break;
            case 411: $text = 'Length Required';
                break;
            case 412: $text = 'Precondition Failed';
                break;
            case 413: $text = 'Request Entity Too Large';
                break;
            case 414: $text = 'Request-URI Too Large';
                break;
            case 415: $text = 'Unsupported Media Type';
                break;
            case 500: $text = 'Internal Server Error';
                break;
            case 501: $text = 'Not Implemented';
                break;
            case 502: $text = 'Bad Gateway';
                break;
            case 503: $text = 'Service Unavailable';
                break;
            case 504: $text = 'Gateway Time-out';
                break;
            case 505: $text = 'HTTP Version not supported';
                break;
            default:
                $text = 'Unknown http status code "'.htmlentities($code).'"';
                break;
        }
        return $text;
    }

    /**
     * Make Link from alle evidencies names
     * 
     * @param string $formated
     * @return string with links
     */
    public function addEvidenciesLinks($formated,$separator = '"')
    {
        $links = [];
        $names = [];
        foreach (\FlexiPeeHP\EvidenceList::$name as $code => $name){
            $names[] = $separator.$code.$separator;
            $links[] = $separator.self::addEvidenceLink($code,$name).$separator;
        }
        return str_replace($names , $links, $formated);
    }

    /**
     * Make link from evidence name
     * 
     * @param string $evidence
     * @param string $name Human readable name
     * @return string hyperlink code to evidence
     */
    static public function addEvidenceLink($evidence,$name = ''){
        return '<a href="evidence.php?evidence='.$evidence.'" title="'.$name.'">'.$evidence.'</a>';
    }
    
}
