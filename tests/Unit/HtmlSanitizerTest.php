<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

final class HtmlSanitizerTest extends TestCase
{
    public function testKeepsWhitelistedStructuralTags(): void
    {
        $html = '<p>Hello</p><h2>Section</h2><ul><li>One</li></ul>';

        self::assertSame($html, HtmlSanitizer::articleBody($html));
    }

    public function testStripsScriptAndIframe(): void
    {
        $html = '<p>Before</p><script>alert(1)</script><iframe src="x"></iframe><p>After</p>';

        $clean = HtmlSanitizer::articleBody($html);

        self::assertStringNotContainsString('<script', $clean);
        self::assertStringNotContainsString('<iframe', $clean);
        self::assertStringNotContainsString('alert(1)', $clean);
        self::assertStringContainsString('<p>Before</p>', $clean);
        self::assertStringContainsString('<p>After</p>', $clean);
    }

    public function testStripsAttributesIncludingEventHandlers(): void
    {
        $html = '<p onclick="evil()" style="color:red">Hi</p>';

        $clean = HtmlSanitizer::articleBody($html);

        self::assertSame('<p>Hi</p>', $clean);
    }

    public function testDropsLinksBecauseTheyAreNotWhitelisted(): void
    {
        $html = '<p>See <a href="javascript:alert(1)">link</a> here</p>';

        $clean = HtmlSanitizer::articleBody($html);

        self::assertStringNotContainsString('<a', $clean);
        self::assertStringNotContainsString('javascript:', $clean);
        self::assertStringContainsString('link', $clean);
    }
}
