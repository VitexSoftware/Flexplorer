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

namespace Flexplorer;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class QueryEditorTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testPropertyEndpointReturnsLibraryMetadata(): void
    {
        chdir(__DIR__.'/../../../src');
        $_GET = ['evidence' => 'custom-button'];
        $oPage = new class() {
            public function onlyForLogged(): bool
            {
                return false;
            }
        };
        ob_start();

        include 'evidence-properties.php';
        $metadata = json_decode(ob_get_clean(), true, 512, \JSON_THROW_ON_ERROR);
        $properties = array_column($metadata, null, 'name');

        self::assertSame('string', $properties['url']['type']);
        self::assertSame('integer', $properties['id']['type']);
    }

    #[RunInSeparateProcess]
    public function testSubmittedBodyIsNotDecodedTwice(): void
    {
        new ui\WebPage();
        $body = '{"winstrom":{"custom-button":[{"id":"180","url":"https://example.test/?q=a+b%20c"}]}}';
        $_REQUEST = ['url' => 'https://example.test/custom-button/180.json', 'method' => 'PUT', 'format' => 'json', 'body' => $body];
        $sender = $this->getMockBuilder(Flexplorer::class)->onlyMethods(['doCurlRequest'])->getMock();
        $sender->expects(self::once())->method('doCurlRequest')
            ->with($_REQUEST['url'], 'PUT', 'json')->willReturn(200);

        $sender->performQuery();

        self::assertSame($body, $sender->getPostFields());
    }

    #[RunInSeparateProcess]
    public function testResponseCopyPreservesXmlMarkupAndEntities(): void
    {
        new ui\WebPage();
        $body = '<winstrom><custom-button><url>https://example.test/?a=1&amp;b=2</url></custom-button></winstrom>';
        $sender = $this->getMockBuilder(\AbraFlexi\RW::class)->disableOriginalConstructor()
            ->onlyMethods(['getResponseFormat'])->getMock();
        $sender->expects(self::once())->method('getResponseFormat')->willReturn('xml');
        $sender->lastResponseCode = 200;
        $sender->lastCurlResponse = $body;
        $sender->url = 'https://example.test';
        $sender->curlInfo = ['url' => 'https://example.test', 'content_type' => 'application/xml'];

        $html = (string) new ui\ShowResponse($sender);
        $document = new \DOMDocument();
        $document->loadHTML($html, \LIBXML_NOERROR | \LIBXML_NOWARNING);
        $xpath = new \DOMXPath($document);

        self::assertSame($body, $xpath->query('//*[@id="formatedResponse"]')->item(0)->textContent);
    }
}
