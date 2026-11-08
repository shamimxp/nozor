<?php

namespace App\Http\Middleware;

use App\Support\CrawlerDetector;
use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession as FrameworkStartSession;
use Illuminate\Support\Facades\RateLimiter;

class StartSession extends FrameworkStartSession
{
    /**
     * Written by the framework on every request. They are not a reason to
     * store a brand-new guest session.
     *
     * @var list<string>
     */
    private const FRAMEWORK_KEYS = ['_token', '_previous', '_flash'];

    /**
     * New empty guest sessions allowed per IP per minute. A browser creates
     * one, then sends the cookie back. A client that never stores cookies
     * would otherwise insert a row on every request.
     */
    private const NEW_GUEST_SESSIONS_PER_MINUTE = 30;

    protected function handleStatefulRequest(Request $request, $session, Closure $next)
    {
        $request->setLaravelSession(
            $this->startSession($request, $session)
        );

        $this->collectGarbage($session);

        $response = $next($request);

        $this->storeCurrentUrl($request, $session);

        if ($this->shouldDiscardNewGuestSession($request, $session)) {
            return $response;
        }

        $this->addCookieToResponse($response, $session);

        $this->saveSession($request);

        return $response;
    }

    /**
     * Skip the database write when this request did not arrive with a session
     * cookie, stored nothing of its own, and is a crawler, a prefetch, or an
     * IP already creating empty sessions far faster than a person can.
     * The response is still returned unchanged.
     */
    protected function shouldDiscardNewGuestSession(Request $request, Session $session): bool
    {
        if ($request->cookies->has($session->getName())) {
            return false;
        }

        if ($this->hasApplicationData($session)) {
            return false;
        }

        if ($request->prefetch() || app(CrawlerDetector::class)->matches($request->userAgent())) {
            return true;
        }

        return $this->newGuestSessionRateExceeded($request);
    }

    protected function hasApplicationData(Session $session): bool
    {
        foreach (array_keys($session->all()) as $key) {
            if (in_array($key, self::FRAMEWORK_KEYS, true) || str_starts_with($key, 'PHPDEBUGBAR')) {
                continue;
            }

            return true;
        }

        return false;
    }

    protected function newGuestSessionRateExceeded(Request $request): bool
    {
        $ip = (string) $request->ip();
        if ($ip === '') {
            return false;
        }

        try {
            $key = 'guest-session:'.$ip;

            if (RateLimiter::tooManyAttempts($key, self::NEW_GUEST_SESSIONS_PER_MINUTE)) {
                return true;
            }

            RateLimiter::hit($key, 60);

            return false;
        } catch (\Throwable) {
            return false;
        }
    }
}
