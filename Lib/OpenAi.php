<?php

declare(strict_types=1);

namespace VisitMarche\ThemeWp\Lib;

use RuntimeException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use VisitMarche\ThemeWp\Enums\LanguageEnum;

class OpenAi
{
    private const string OPENAI_API_URL = 'https://api.openai.com/v1/chat/completions';
    private const string DEFAULT_MODEL = 'gpt-4o-mini';
    private const int DEFAULT_CACHE_TTL = 86400 * 30; // 30 days

    private readonly FilesystemAdapter $cache;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
        private readonly string $model = self::DEFAULT_MODEL,
        private readonly int $cacheTtl = self::DEFAULT_CACHE_TTL,
    ) {
        $this->cache = new FilesystemAdapter(
            namespace: 'openai_translations',
            defaultLifetime: $this->cacheTtl,
            directory: defined('ABSPATH') ? ABSPATH . '../var/cache' : sys_get_temp_dir(),
        );
    }

    /**
     * Translate text to the given language. Returns cached result if available.
     */
    public function translate(string $text, LanguageEnum $language): string
    {
        if (trim($text) === '') {
            return '';
        }

        $cacheKey = $this->buildCacheKey($text, $language);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($text, $language): string {
            $item->expiresAfter($this->cacheTtl);

            return $this->callOpenAi($text, $language);
        });
    }

    /**
     * Translate and bypass cache (forces a fresh API call, then caches the result).
     */
    public function translateFresh(string $text, LanguageEnum $language): string
    {
        if (trim($text) === '') {
            return '';
        }

        $cacheKey = $this->buildCacheKey($text, $language);
        $this->cache->deleteItem($cacheKey);

        return $this->translate($text, $language);
    }

    public function clearCache(): bool
    {
        return $this->cache->clear();
    }

    private function callOpenAi(string $text, LanguageEnum $language): string
    {
        $prompt = sprintf(
            'Translate the following text to %s. Return ONLY the translated text, no explanations or quotes.',
            $language->label(),
        );

        $response = $this->httpClient->request('POST', self::OPENAI_API_URL, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $prompt],
                    ['role' => 'user', 'content' => $text],
                ],
                'temperature' => 0.3,
            ],
        ]);

        $data = $response->toArray();

        return trim($data['choices'][0]['message']['content'] ?? throw new RuntimeException(
            'Unexpected OpenAI response: no content in choices',
        ));
    }

    private function buildCacheKey(string $text, LanguageEnum $language): string
    {
        return sprintf('translation_%s_%s', $language->value, md5($text));
    }

    /**
     * Factory: create an instance using $_ENV and a fresh HttpClient.
     */
    public static function create(?string $model = null): self
    {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? throw new RuntimeException(
            'OPENAI_API_KEY is not set in environment',
        );

        return new self(
            httpClient: HttpClient::create(),
            apiKey: $apiKey,
            model: $model ?? self::DEFAULT_MODEL,
        );
    }
}
