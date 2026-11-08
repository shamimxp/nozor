<?php

namespace App\Support;

use Illuminate\Database\Query\Builder;

class CrawlerDetector
{
    /**
     * Substrings that identify non-interactive clients. Matched case-insensitively.
     * In-app browsers (WhatsApp, Facebook app) are intentionally absent so a
     * person opening the shop from a chat still receives a normal session.
     *
     * @var list<string>
     */
    public const MARKERS = [
        'bingbot',
        'bingpreview',
        'msnbot',
        'adidxbot',
        'microsoftpreview',
        'googlebot',
        'adsbot-google',
        'mediapartners-google',
        'storebot-google',
        'google-inspectiontool',
        'facebookexternalhit',
        'facebot',
        'twitterbot',
        'linkedinbot',
        'slackbot',
        'telegrambot',
        'discordbot',
        'applebot',
        'duckduckbot',
        'baiduspider',
        'yandexbot',
        'ahrefsbot',
        'semrushbot',
        'dotbot',
        'petalbot',
        'bytespider',
        'gptbot',
        'chatgpt-user',
        'claudebot',
        'amazonbot',
        'ccbot',
        'mj12bot',
        'seznambot',
        'qwantify',
        'pinterestbot',
        'uptimerobot',
        'pingdom',
        'headlesschrome',
        'phantomjs',
        'screaming frog',
        'ia_archiver',
        'archive.org_bot',
        'spider',
        'crawler',
    ];

    public function matches(?string $userAgent): bool
    {
        $ua = strtolower(trim((string) $userAgent));

        if ($ua === '') {
            return true;
        }

        foreach (self::MARKERS as $marker) {
            if (str_contains($ua, $marker)) {
                return true;
            }
        }

        // "compatible; SomeBot/1.0; +http://..." is the classic crawler token.
        // A normal browser user agent does not carry both.
        return str_contains($ua, 'compatible;')
            && (str_contains($ua, 'http://') || str_contains($ua, 'https://'));
    }

    /**
     * Limit a sessions-table query to rows written by crawlers.
     */
    public function constrain(Builder $query): void
    {
        $query->where(function (Builder $inner) {
            $inner->where(function (Builder $markers) {
                foreach (self::MARKERS as $index => $marker) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $markers->{$method}('user_agent', 'like', '%'.$this->escapeLike($marker).'%');
                }
            });

            $inner->orWhere(function (Builder $classic) {
                $classic->where('user_agent', 'like', '%compatible;%')
                    ->where(function (Builder $url) {
                        $url->where('user_agent', 'like', '%http://%')
                            ->orWhere('user_agent', 'like', '%https://%');
                    });
            });
        });
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
