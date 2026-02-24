<?php

namespace VisitMarche\ThemeWp\Inc;

class LanguageRouter
{
    public const QUERY_VAR = 'lang';
    public const DEFAULT_LANGUAGE = 'fr';
    public const SUPPORTED_LANGUAGES = ['fr', 'en', 'nl', 'de'];

    private static string $detectedLanguage = self::DEFAULT_LANGUAGE;

    public function __construct()
    {
        $this->stripLanguagePrefix();
    }

    /**
     * Strip the language prefix from REQUEST_URI before WordPress parses it.
     * This runs during functions.php loading, well before WP::parse_request().
     */
    private function stripLanguagePrefix(): void
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $langPattern = '#^/(en|nl|de|fr)(/.*)?$#';

        if (preg_match($langPattern, $path, $matches)) {
            self::$detectedLanguage = $matches[1];
            $newPath = $matches[2] ?? '/';
            if ($newPath === '') {
                $newPath = '/';
            }
            $query = parse_url($uri, PHP_URL_QUERY);
            $_SERVER['REQUEST_URI'] = $newPath . ($query ? '?' . $query : '');

            // Strip from PATH_INFO too — WordPress uses it over REQUEST_URI when set
            if (!empty($_SERVER['PATH_INFO'])) {
                $stripped = preg_replace($langPattern, '$2', $_SERVER['PATH_INFO']);
                // If only "/" or empty remains, unset to let WP use REQUEST_URI instead
                $_SERVER['PATH_INFO'] = ($stripped === '/' || $stripped === '' || $stripped === null) ? '' : $stripped;
            }
        }
    }

    public static function getCurrentLanguage(): string
    {
        return self::$detectedLanguage;
    }

    public static function url(string $path): string
    {
        $lang = self::getCurrentLanguage();
        $path = ltrim($path, '/');

        if ($lang === self::DEFAULT_LANGUAGE) {
            return '/' . $path;
        }

        return '/' . $lang . '/' . $path;
    }

    public static function currentPath(): string
    {
        global $wp;

        return $wp->request ?? '';
    }
}
