<?php

namespace App\Helpers;

class ArticleHelper
{
    /**
     * Sanitize article content - remove scripts, ads, comments
     */
    public static function sanitizeContent(string $content): string
    {
        // Remove script tags and content (multiple patterns for robustness)
        $content = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/si', '', $content);
        $content = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/si', '', $content);

        // Remove style tags and content
        $content = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/si', '', $content);
        $content = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/si', '', $content);

        // Remove noscript, iframe, form, object, embed tags and content
        $content = preg_replace('/<noscript\b[^>]*>[\s\S]*?<\/noscript>/si', '', $content);
        $content = preg_replace('/<iframe\b[^>]*>[\s\S]*?<\/iframe>/si', '', $content);
        $content = preg_replace('/<iframe\b[^>]*\/>/si', '', $content);
        $content = preg_replace('/<form\b[^>]*>[\s\S]*?<\/form>/si', '', $content);
        $content = preg_replace('/<object\b[^>]*>[\s\S]*?<\/object>/si', '', $content);
        $content = preg_replace('/<embed\b[^>]*>/si', '', $content);

        // Remove HTML comments
        $content = preg_replace('/<!--[\s\S]*?-->/s', '', $content);

        // Remove JavaScript comments
        $content = preg_replace('/\/\*[\s\S]*?\*\//s', '', $content);

        // Remove all event handler attributes (onclick, onload, onerror, etc.)
        $content = preg_replace('/\s(on[a-z]+|data-[a-z-]+)="[^"]*"/i', '', $content);
        $content = preg_replace("/\s(on[a-z]+|data-[a-z-]+)='[^']*'/i", '', $content);

        // Remove JavaScript code blocks that leaked as plain text (after strip_tags removed <script>)
        $content = self::removeJavaScriptBlocks($content);

        // Remove known navigation/menu text from source websites
        $content = self::removeNavigationText($content);

        // Remove tags with javascript: URLs (e.g. <p href="javascript:;">)
        $content = preg_replace('/<[^>]+href\s*=\s*["\']javascript:[^"\']*["\'][^>]*>.*?<\/[^>]+>/si', '', $content);
        $content = preg_replace('/<[^>]+href\s*=\s*["\']javascript:[^"\']*["\'][^>]*\/>/si', '', $content);

        // Remove small remaining JS fragments (e.g., "if (i", "})()", etc.)
        $content = preg_replace('/^\s*\}\s*\)\s*\(\s*\)\s*;?\s*$/m', '', $content);
        $content = preg_replace('/^\s*if\s*\(\s*\w{1,3}\s*$/m', '', $content);
        $content = preg_replace('/^\s*[\}\)\];]+\s*$/m', '', $content);

        // Remove excessive whitespace and blank lines
        $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content);
        $content = trim($content);

        return $content;
    }

    /**
     * Remove JavaScript code blocks that leaked as plain text.
     * When strip_tags() removes <script> tags, the JS code inside survives as text.
     * This method detects and removes contiguous blocks of JS-like lines.
     */
    public static function removeJavaScriptBlocks(string $content): string
    {
        $lines = explode("\n", $content);
        $cleanLines = [];
        $buffer = [];
        $jsLineCount = 0;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Empty lines go to buffer
            if ($trimmed === '') {
                $buffer[] = $line;
                continue;
            }

            if (self::isJavaScriptLine($trimmed)) {
                $jsLineCount++;
                $buffer[] = $line;
            } else {
                // Non-JS line: flush buffer if it wasn't a JS block
                if ($jsLineCount < 2) {
                    // Not enough JS lines to be a code block — keep the buffer
                    foreach ($buffer as $bLine) {
                        $cleanLines[] = $bLine;
                    }
                }
                // Otherwise discard the whole buffer (JS block removed)

                // Start new buffer with this non-JS line
                $buffer = [$line];
                $jsLineCount = 0;
            }
        }

        // Handle remaining buffer
        if ($jsLineCount < 2) {
            foreach ($buffer as $bLine) {
                $cleanLines[] = $bLine;
            }
        }

        return implode("\n", $cleanLines);
    }

    /**
     * Check if a single line looks like JavaScript code
     */
    private static function isJavaScriptLine(string $line): bool
    {
        // Skip lines that are clearly HTML tags (article content)
        if (preg_match('/^<(p|h[1-6]|div|figure|figcaption|img|ul|ol|li|blockquote|strong|em|br|a|table)\b/i', $line)) {
            return false;
        }

        $jsPatterns = [
            // Variable declarations and assignments
            '/\b(var|let|const)\s+\w+/',
            // Function declarations/expressions
            '/\bfunction\s*\(/',
            '/=>\s*\{/',
            // Control flow with JS syntax
            '/\bif\s*\(.+\)\s*\{?\s*$/',
            '/\bfor\s*\(.+\)\s*\{?\s*$/',
            '/\bwhile\s*\(.+\)\s*\{?\s*$/',
            '/\btry\s*\{/',
            '/\bcatch\s*\(/',
            '/\breturn\s*;/',
            '/\breturn\s+(true|false|null|undefined)\s*;?$/',
            // DOM manipulation
            '/\bdocument\.\w+/',
            '/\bwindow\.\w+/',
            '/\.parentNode\b/',
            '/\.childNodes\b/',
            '/\.nodeName\b/',
            '/\.nodeType\b/',
            '/\.createElement\(/',
            '/\.insertBefore\(/',
            '/\.appendChild\(/',
            '/\.removeChild\(/',
            '/\.getAttribute\(/',
            '/\.setAttribute\(/',
            '/\.getElementById\(/',
            '/\.querySelector\w*\(/',
            '/\.classList\b/',
            '/\.innerHTML\b/',
            '/\.textContent\b/',
            // jQuery
            '/\$\s*\([\'\"]/',
            '/\$\(.*\)\.\w+\(/',
            '/\.indexOf\s*\(/',
            '/\.toLowerCase\s*\(/',
            '/\.toUpperCase\s*\(/',
            // Common JS methods and keywords
            '/\btypeof\s+\w+/',
            '/\b(true|false)\s*;?\s*$/',
            '/!==|===/',
            '/\|\|\s*\[\]/',
            '/\.push\s*\(/',
            '/\.length\b/',
            '/\.attr\s*\(/',
            // JS object notation / config
            '/^\s*\w+\s*:\s*[\'\"]\w/',
            '/^\s*(mode|placement|target_type|container)\s*:/',
            // Braces-only lines (common in code blocks)
            '/^\s*[\{\}]\s*$/',
            '/^\s*\}\s*\)\s*;?\s*$/',    // }); or })
            '/^\s*\)\s*;?\s*$/',          // ); or )
            // Ad/tracking JS
            '/runinit/',
            '/adsbygoogle/',
            '/googletag/',
            '/\.push\(\{/',
            '/taboola/',
            '/_chkPrLink/',
            '/mutexAds/',
            // JS comments
            '/^\s*\/\//',
        ];

        foreach ($jsPatterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove navigation/menu text that leaked from source websites
     */
    public static function removeNavigationText(string $content): string
    {
        $navPatterns = [
            // Vietnamese nav items commonly found in crawled content
            '/^\s*(Bình luận mới được duyệt|Xem tất cả|Thông tin tài khoản)\s*$/m',
            '/^\s*(Đổi mật khẩu|Tin đã lưu|Tin đã xem|Đăng xuất|Đăng nhập|Đăng ký)\s*$/m',
            '/^\s*(Trang chủ|Liên hệ|Giới thiệu|Điều khoản|Chính sách)\s*$/m',
            '/^\s*(Tin mới nhất|Tin nổi bật|Bài viết liên quan|Chia sẻ bài viết)\s*$/m',
            '/^\s*(Facebook|Twitter|LinkedIn|Zalo|Copy link|Chia sẻ)\s*$/m',
            '/^\s*ĐỌC NGAY\s*$/m',
            '/^\s*Theo dõi.*$/m',
            // Footer items from Vietnamese news sites
            '/^\s*(Đặt báo|Tòa soạn|Chính sách bảo mật|Theo dõi báo trên)\s*$/m',
            '/^\s*(Quảng cáo|RSS|Liên hệ quảng cáo|Điều khoản sử dụng)\s*$/m',
            '/^\s*(Tổng biên tập|Giấy phép|Trụ sở|Hotline|Email liên hệ)\s*$/m',
            // Comment system / interaction text
            '/^\s*Bạn không thể gửi bình luận.*$/m',
            '/^\s*\d+\s*giây nữa\.?\s*$/m',
            '/^\s*(Gửi bình luận|Đăng nhập để bình luận)\s*$/m',
            '/^\s*(Quan tâm nhất|Mới nhất)\s*$/m',
            '/^\s*(Xem thêm bình luận|Bình luận của bạn)\s*$/m',
            '/^\s*Bình luận\s*(\(\d+\))?\s*$/m',
            // Common English nav items
            '/^\s*(Home|Contact|About|Terms|Privacy|Login|Logout|Sign in|Sign up|Register)\s*$/mi',
            '/^\s*(Share|Comments?|Read more|Load more|View all|Subscribe)\s*$/mi',
        ];

        foreach ($navPatterns as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }

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
        // First sanitize (removes scripts, styles, JS blocks, nav text, etc.)
        $content = self::sanitizeContent($content);

        // Decode HTML entities
        $content = self::decodeHtmlEntities($content);

        // Define allowed HTML tags for article content
        $allowedTags = '<p><br><strong><b><em><i><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote><figure><figcaption><table><thead><tbody><tr><th><td><span><div><sub><sup><hr>';

        // Check if content already contains HTML block elements
        if (preg_match('/<(p|div|br|h[1-6]|ul|ol|table|blockquote|figure)\b/i', $content)) {
            // Content already has HTML structure — strip unsafe tags, keep formatting
            $content = strip_tags($content, $allowedTags);
        } else {
            // Plain text content — convert to HTML paragraphs
            $content = strip_tags($content, $allowedTags);

            $paragraphs = array_filter(
                array_map('trim', explode("\n\n", $content)),
                fn($p) => !empty($p) && strlen($p) > 10
            );

            $html = '';
            foreach ($paragraphs as $para) {
                $para = nl2br($para);
                $html .= "<p>{$para}</p>\n";
            }
            $content = $html;
        }

        // Final cleanup: remove navigation/UI text that survived inside HTML tags
        $content = self::removeNavigationText($content);

        // Remove HTML elements containing comment-section / UI text
        $content = preg_replace('/<p[^>]*>\s*Bình luận\s*(\(\d+\))?\s*<\/p>/si', '', $content);
        $content = preg_replace('/<p[^>]*>\s*(Gửi bình luận|Đăng nhập để bình luận|Xem thêm bình luận)\s*<\/p>/si', '', $content);
        $content = preg_replace('/<p[^>]*>\s*(Quan tâm nhất|Mới nhất)\s*<\/p>/si', '', $content);

        // Remove empty tags left behind
        $content = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $content);
        $content = preg_replace('/<div[^>]*>\s*<\/div>/i', '', $content);
        $content = preg_replace('/<span[^>]*>\s*<\/span>/i', '', $content);

        // Final whitespace cleanup
        $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content);
        $content = trim($content);

        // Clean image attributes that cause sizing issues
        $content = self::cleanImageAttributes($content);

        return $content;
    }

    /**
     * Clean inline width/height/style attributes from <img> tags
     * so images are responsive and fit within the article container.
     */
    public static function cleanImageAttributes(string $content): string
    {
        // Remove width and height attributes (both quoted and unquoted)
        $content = preg_replace('/<img([^>]*)\s+width\s*=\s*["\']?\d+["\']?/si', '<img$1', $content);
        $content = preg_replace('/<img([^>]*)\s+height\s*=\s*["\']?\d+["\']?/si', '<img$1', $content);

        // Remove custom w= and h= attributes (from some CMS like Thanh Nien)
        $content = preg_replace('/<img([^>]*)\s+w\s*=\s*["\']?\d+["\']?/si', '<img$1', $content);
        $content = preg_replace('/<img([^>]*)\s+h\s*=\s*["\']?\d+["\']?/si', '<img$1', $content);

        // Remove inline style with fixed dimensions on img tags
        $content = preg_replace('/<img([^>]*)\s+style\s*=\s*["\'][^"\']*["\']([^>]*)>/si', '<img$1$2>', $content);

        // Remove srcset to avoid loading wrong resolution
        $content = preg_replace('/<img([^>]*)\s+srcset\s*=\s*["\'][^"\']*["\']([^>]*)>/si', '<img$1$2>', $content);

        return $content;
    }
}
