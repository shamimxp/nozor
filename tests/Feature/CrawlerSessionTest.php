<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CrawlerSessionTest extends TestCase
{
    private const BINGBOT = 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36';

    private const CHROME = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get('/_session-probe', fn () => 'ok');
        Route::middleware('web')->get('/_session-write', function () {
            session(['coupon' => ['code' => 'SAVE']]);

            return 'ok';
        });
    }

    public function test_bingbot_receives_the_page_without_a_new_session(): void
    {
        $response = $this->withHeader('User-Agent', self::BINGBOT)->get('/_session-probe');

        $response->assertOk();
        $response->assertCookieMissing($this->sessionCookie());
    }

    public function test_a_normal_browser_still_receives_a_session_cookie(): void
    {
        $response = $this->withHeader('User-Agent', self::CHROME)->get('/_session-probe');

        $response->assertOk();
        $response->assertCookie($this->sessionCookie());
    }

    public function test_a_crawler_that_stores_application_data_keeps_its_session(): void
    {
        $response = $this->withHeader('User-Agent', self::BINGBOT)->get('/_session-write');

        $response->assertOk();
        $response->assertCookie($this->sessionCookie());
    }

    public function test_an_existing_browser_session_is_kept(): void
    {
        $first = $this->withHeader('User-Agent', self::CHROME)->get('/_session-probe');
        $cookie = $first->getCookie($this->sessionCookie());

        $second = $this->withHeader('User-Agent', self::CHROME)
            ->withCookie($cookie->getName(), $cookie->getValue())
            ->get('/_session-probe');

        $second->assertOk();
        $second->assertCookie($this->sessionCookie());
    }

    public function test_cookie_less_flood_stops_creating_sessions_without_rejecting_the_request(): void
    {
        $cookie = $this->sessionCookie();
        $last = null;

        for ($i = 0; $i < 31; $i++) {
            $last = $this->withHeader('User-Agent', self::CHROME)->get('/_session-probe');
            $last->assertOk();
        }

        $last->assertCookieMissing($cookie);
    }

    private function sessionCookie(): string
    {
        return (string) config('session.cookie');
    }
}
