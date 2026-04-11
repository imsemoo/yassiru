<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Primary Meta — Dynamic per page for crawlers -->
    <title>{{ $meta['title'] ?? 'يسّرو — منصة تيسير الزواج الأولى في العالم الإسلامي' }}</title>
    <meta name="description" content="{{ $meta['description'] ?? 'يسّرو تُيسّر زواجك من الألف للياء: دورة تأهيلية شرعية ونفسية، توفيق عبر معرّفين موثوقين، صندوق تعاوني بدون فوائد، وأعراس جماعية توفّر حتى 70%. ابدأ رحلتك الآن مجاناً.' }}">
    <meta name="keywords" content="تيسير الزواج, زواج إسلامي, صندوق تعاوني, أعراس جماعية, تأهيل زواجي, توفيق زواج, مهر, تكاليف الزواج, يسرو, yassiru">
    <meta name="author" content="يسّرو">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $meta['title'] ?? 'يسّرو — منصة تيسير الزواج الأولى في العالم الإسلامي' }}">
    <meta property="og:description" content="{{ $meta['description'] ?? 'دورة تأهيلية + توفيق عبر معرّفين + صندوق تعاوني + أعراس جماعية. وفّر حتى 70% من تكاليف زواجك.' }}">
    <meta property="og:image" content="{{ asset($meta['og_image'] ?? 'logo.svg') }}">
    <meta property="og:locale" content="ar_AR">
    <meta property="og:site_name" content="يسّرو">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta['title'] ?? 'يسّرو — منصة تيسير الزواج' }}">
    <meta name="twitter:description" content="{{ $meta['description'] ?? 'وفّر حتى 70% من تكاليف زواجك مع يسّرو. دورة تأهيلية + صندوق تعاوني + أعراس جماعية.' }}">
    <meta name="twitter:image" content="{{ asset($meta['og_image'] ?? 'logo.svg') }}">

    <!-- Favicon & PWA -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Theme Color -->
    <meta name="theme-color" content="#0d7377">
    <meta name="msapplication-TileColor" content="#0d7377">

    <!-- Fonts: Preconnect + Load -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Amiri:wght@400;700&family=Aref+Ruqaa:wght@400;700&display=swap" rel="stylesheet">

    @vite(['resources/js/app.js'])

    <!-- Google Analytics -->
    @if(config('app.env') === 'production')
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id', '') }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ config('services.google.analytics_id', '') }}');
    </script>
    @endif

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    @php
    echo json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'يسّرو',
        'alternateName' => 'Yassiru',
        'url' => url('/'),
        'logo' => asset('logo.svg'),
        'description' => 'منصة تيسير الزواج الأولى في العالم الإسلامي — تأهيل، توفيق، تمويل تعاوني، أعراس جماعية',
        'sameAs' => [],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'email' => 'islam@yassiru.com',
            'contactType' => 'customer service',
            'availableLanguage' => 'Arabic',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    @endphp
    </script>
</head>
<body>
    <div id="app"></div>

    <noscript>
        <div style="text-align: center; padding: 2rem; font-family: Cairo, sans-serif; direction: rtl;">
            <h1>يسّرو — منصة تيسير الزواج</h1>
            <p>يرجى تفعيل JavaScript لاستخدام المنصة.</p>
        </div>
    </noscript>

    <!-- Service Worker Registration (production only) -->
    @if(config('app.env') === 'production')
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
      }
    </script>
    @endif
</body>
</html>
