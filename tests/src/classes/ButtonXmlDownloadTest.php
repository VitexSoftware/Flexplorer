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

class ButtonXmlDownloadTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testDownloadContainsUniqueValidButtonsForEveryEvidence(): void
    {
        chdir(__DIR__.'/../../../src');
        $_REQUEST = ['type' => 'evidence', 'operation' => 'download'];
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/getbuttonxml.php?type=evidence&operation=download';
        unset($_SERVER['REQUEST_SCHEME'], $_SERVER['HTTPS']);
        $oPage = new \Ease\Document();

        ob_start();

        include 'getbuttonxml.php';
        $output = ob_get_clean();

        $document = new \DOMDocument();
        self::assertTrue($document->loadXML($output));
        self::assertSame('winstrom', $document->documentElement->tagName);
        $buttons = $document->getElementsByTagName('custom-button');
        self::assertCount(\count(\AbraFlexi\EvidenceList::$name) * 4, $buttons);

        $codes = [];
        $urls = [];

        foreach ($buttons as $button) {
            $code = $button->getElementsByTagName('id')->item(0)->textContent;
            self::assertStringStartsWith('code:', $code);
            self::assertLessThanOrEqual(20, \strlen(substr($code, 5)));
            self::assertArrayNotHasKey($code, $codes);
            $codes[$code] = true;
            $urls[] = $button->getElementsByTagName('url')->item(0)->textContent;
        }

        self::assertContains('http://localhost/evidence.php?evidence=adresar', $urls);
        self::assertContains('http://localhost/editor.php?evidence=adresar&id=${object.id}', $urls);
        self::assertContains('http://localhost/query.php?format=json&show=result&evidence=adresar&id=${objectIds}', $urls);
        self::assertContains('http://localhost/query.php?format=xml&show=result&evidence=adresar&id=${objectIds}', $urls);
    }
}
