<?php
/*
<winstrom version="1.0">
  <custom-button>
    <id>code:PRESYPAT-POLOZKY</id>
    <url>
      <![CDATA[__URL__?objid=${object.id}]]>
    </url>
    <title>ORGANIZOVAT</title>
    <description>Zvolit položky přijaté objednávky ze kterých bude sestavena objednávka vydaná </description>
    <evidence>objednavka-prijata</evidence>
    <location>detail</location>
    <browser>desktop</browser>
  </custom-button>
</winstrom>
 */

namespace Flexplorer\xml;

/**
 * Description of CustomButton
 *
 * @author vitex
 */
class FelexiBeeButtonXML extends CustomButton
{
    public function __construct($code,$url,$title,$desc,$evidence,$location, $browser)
    {
        parent::__construct($this->tagType, null, [
            new IdTag( \FlexiPeeHP\FlexiBeeRO::code($code) ),
            new UrlTag($url),
            new \Ease\Html\TitleTag($title),
            new DescriptionTag($desc),
            new EvidenceTag($evidence),
            new LocationTag($location),
            new BrowserTag($browser)
        ]);
    }
}
