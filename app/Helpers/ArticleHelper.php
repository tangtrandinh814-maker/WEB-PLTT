<?php

namespace App\Helpers;

class ArticleHelper
{
    /**
     * Sanitize article content - remove scripts, ads, comments
     */
    public static function sanitizeContent(string $content): string
    {
        // Remove script tags and content
        $content = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/si', '', $content);

        // Remove style tags and content
        $content = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/si', '', $content);

        // Remove HTML comments
        $content = preg_replace('/<!--(.*)-->/Uis', '', $content);

        // Remove JavaScript comments starting with //
        $content = preg_replace('/^\s*\/\/.*$/m', '', $content);

        // Remove lines with ads keywords
        $content = preg_replace('/^.*?(ads|quảng cáo|taboola|arfAsync).*$/im', '', $content);

        // Remove data attributes and onclick handlers
        $content = preg_replace('/\s(data-[a-z-]+|onclick|onload|onerror)="[^"]*"/i', '', $content);

        // Remove excessive whitespace and blank lines
        $content = preg_replace('/\n\s*\n/', "\n", $content);
        $content = trim($content);

        return $content;
    }

    /**
     * Clean HTML entities in content
     */
    public static function decodeHtmlEntities(string $content): string
    {
        return html_entity_decode($content, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Format article content for display
     */
    public static function formatContent(string $content): string
    {
        // First sanitize
        $content = self::sanitizeContent($content);

        // Decode HTML entities
        $content = self::decodeHtmlEntities($content);

        // Convert line breaks to paragraphs
        $paragraphs = array_filter(
            array_map('trim', explode("\n\n", $content)),
            fn($p) => !empty($p) && strlen($p) > 10
        );

        $html = '';
        foreach ($paragraphs as $para) {
            $para = nl2br(htmlspecialchars($para, ENT_QUOTES, 'UTF-8'));
            $html .= "<p>{$para}</p>\n";
        }

        return $html;
    }
}
