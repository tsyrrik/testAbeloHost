<?php
declare(strict_types=1);

namespace App\Support;

final class HtmlSanitizer
{
    private const ALLOWED_TAGS = '<p><h2><h3><h4><ul><ol><li><strong><em><br><blockquote><code><pre>';

    private const DANGEROUS_BLOCKS = 'script|style|iframe|object|embed|svg|noscript|template';

    public static function articleBody(string $html): string
    {
        $html = (string) preg_replace(
            '#<(' . self::DANGEROUS_BLOCKS . ')\b[^>]*>.*?</\1\s*>#is',
            '',
            $html,
        );

        $stripped = strip_tags($html, self::ALLOWED_TAGS);

        return (string) preg_replace('/<(\w+)\s[^>]*>/i', '<$1>', $stripped);
    }
}
