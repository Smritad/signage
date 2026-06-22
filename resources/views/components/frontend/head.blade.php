<!DOCTYPE html>
<html lang="en">
@php
    use App\Models\SeoTag;
    use Illuminate\Support\Str;

    /* ---- Dynamic SEO: match the current page against seo_details (by URL path) ---- */
    $__curPath  = trim(request()->path(), '/');                 // e.g. 'contact-us'; '' = home
    $__basePath = trim((string) parse_url(config('app.url'), PHP_URL_PATH), '/'); // e.g. 'signage'

    $__seo = null;
    $__bestLen = -1;
    foreach (SeoTag::whereNull('deleted_by')->get() as $__row) {
        $__p = trim((string) parse_url($__row->page_url, PHP_URL_PATH), '/');

        if ($__curPath === '') {
            // Home page: stored URL is the site root (empty path or just the app base)
            $__match = ($__p === '' || $__p === $__basePath);
        } else {
            // Match exact path or trailing segment(s); leading slash avoids partial-word hits
            $__match = ($__p === $__curPath) || Str::endsWith('/' . $__p, '/' . $__curPath);
        }

        // Prefer the most specific (longest) matching path
        if ($__match && strlen($__p) > $__bestLen) {
            $__seo     = $__row;
            $__bestLen = strlen($__p);
        }
    }

    $metaTitle       = $__seo->meta_title       ?? 'Signage Wellness';
    $metaDescription = $__seo->meta_description ?? null;
    $metaKeywords    = $__seo->meta_keywords    ?? null;
    $metaAuthor      = $__seo->meta_author      ?? null;
    $canonicalTag    = $__seo->canonical_tag    ?? url()->current();
    $hreflangTag     = $__seo->hreflang_tag     ?? null;
@endphp
<head>
    <meta charset="utf-8">
    <title>{{ $metaTitle }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    {{-- ✅ Dynamic SEO meta (managed from backend SEO tab) --}}
    @if($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if($metaKeywords)
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    @if($metaAuthor)
        <meta name="author" content="{{ $metaAuthor }}">
    @endif
    @if($canonicalTag)
        <link rel="canonical" href="{{ $canonicalTag }}">
    @endif
    @if($hreflangTag)
        <link rel="alternate" hreflang="{{ $hreflangTag }}" href="{{ $canonicalTag ?: url()->current() }}">
    @endif

    <!-- ✅ Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('frontend/assets/images/logo/logo.webp') }}">
    <!-- Optional: For wider browser support -->
    <!--
    <link rel="icon" type="image/x-icon" href="{{ asset('frontend/assets/images/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('frontend/assets/images/favicon.png') }}">
    -->

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/icon/icomoon/style.css') }}">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightslider@1.1.6/dist/css/lightslider.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom.css') }}">

    {{-- Page-specific styles moved out of inline <style> blocks --}}
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/pages.css') }}">

    <!--<link rel="stylesheet" href="{{ asset('frontend/assets/css/all-category.css') }}">-->
    <!--<link rel="stylesheet" href="{{ asset('frontend/assets/css/all-sabcat.css') }}">-->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>
