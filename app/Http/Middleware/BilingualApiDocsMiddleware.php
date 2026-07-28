<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BilingualApiDocsMiddleware
{
    /**
     * Intercept Scramble doc responses and filter out the non-preferred language.
     * Language priority: 1) ?lang= query param, 2) lang cookie, 3) Accept-Language header.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->is('docs/api*')) {
            return $response;
        }

        // Determine language: query param > cookie > Accept-Language header
        $lang = $request->query('lang')
            ?? $request->cookie('docs_lang')
            ?? ($request->getPreferredLanguage(['en', 'id']) === 'en' ? 'en' : 'id')
            ?? 'id';

        $lang = in_array($lang, ['en', 'id']) ? $lang : 'id';

        $content = $response->getContent();

        // Try JSON first
        $data = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            array_walk_recursive($data, function (&$value) use ($lang) {
                if (is_string($value)) {
                    $value = $this->filterLang($value, $lang);
                }
            });
            $response->setContent(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        } else {
            // It's the HTML page — filter raw content
            $content = $this->filterLang($content, $lang);
            $response->setContent($content);
        }

        // Persist lang preference via cookie (1 year)
        if ($request->has('lang')) {
            $response->headers->setCookie(
                cookie('docs_lang', $lang, 60 * 24 * 365, '/')
            );
        }

        return $response;
    }

    /**
     * Strip language tags from a string, keeping only the preferred language content.
     */
    private function filterLang(string $text, string $lang): string
    {
        if ($lang === 'en') {
            $text = preg_replace('/<id>.*?<\/id>/s', '', $text);
            $text = preg_replace('/<\/?en>/', '', $text);
        } else {
            $text = preg_replace('/<en>.*?<\/en>/s', '', $text);
            $text = preg_replace('/<\/?id>/', '', $text);
        }
        return trim($text);
    }
}
