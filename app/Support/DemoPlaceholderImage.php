<?php

namespace App\Support;

/**
 * Generates consistent, on-brand placeholder product photos for seeded demo
 * data, reusing the exact icon glyphs already defined in
 * resources/views/components/icon.blade.php so placeholders match the
 * site's own visual language instead of introducing a new style.
 */
class DemoPlaceholderImage
{
    /** @var array<string, array{icon: string, from: string, to: string, color: string, label: string}> */
    private static array $palette = [
        'kancelyariya' => ['icon' => 'pen', 'from' => '#eef2ff', 'to' => '#c7d2fe', 'color' => '#4338ca', 'label' => 'Канцелярия'],
        'ofis-i-biznes' => ['icon' => 'briefcase', 'from' => '#eff6ff', 'to' => '#bfdbfe', 'color' => '#1d4ed8', 'label' => 'Офис и бизнес'],
        'tvorchestvo' => ['icon' => 'palette', 'from' => '#fdf2f8', 'to' => '#fbcfe8', 'color' => '#be185d', 'label' => 'Творчество и хобби'],
        'upakovka' => ['icon' => 'box', 'from' => '#fff7ed', 'to' => '#fed7aa', 'color' => '#c2410c', 'label' => 'Упаковка'],
        'knigi-i-pechat' => ['icon' => 'book', 'from' => '#f0fdf4', 'to' => '#bbf7d0', 'color' => '#15803d', 'label' => 'Книги и печать'],
        'elektronika' => ['icon' => 'cpu', 'from' => '#f5f3ff', 'to' => '#ddd6fe', 'color' => '#6d28d9', 'label' => 'Электроника'],
    ];

    /** @var array<string, string> */
    private static array $iconPaths = [
        'pen' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z"/>',
        'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'palette' => '<circle cx="12" cy="12" r="9"/><circle cx="8" cy="11" r="1.3" fill="currentColor"/><circle cx="11" cy="8" r="1.3" fill="currentColor"/><circle cx="15" cy="8.5" r="1.3" fill="currentColor"/><circle cx="16" cy="12.5" r="1.3" fill="currentColor"/>',
        'box' => '<rect x="3" y="8" width="18" height="13" rx="1.5"/><path d="M3 8 12 3l9 5"/><path d="M12 12v9"/>',
        'book' => '<path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v17H6.5A2.5 2.5 0 0 0 4 21.5Z"/><path d="M4 4.5v17"/>',
        'cpu' => '<rect x="6" y="6" width="12" height="12" rx="1.5"/><rect x="9.5" y="9.5" width="5" height="5"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>',
    ];

    public static function svgFor(?string $categorySlug): string
    {
        $theme = self::$palette[$categorySlug] ?? self::$palette['kancelyariya'];
        $iconPath = self::$iconPaths[$theme['icon']];

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">
    <defs>
        <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="{$theme['from']}"/>
            <stop offset="100%" stop-color="{$theme['to']}"/>
        </linearGradient>
    </defs>
    <rect width="800" height="800" rx="48" fill="url(#bg)"/>
    <circle cx="400" cy="380" r="190" fill="rgba(255,255,255,.45)"/>
    <g transform="translate(400,380) scale(10) translate(-12,-12)" color="{$theme['color']}" stroke="{$theme['color']}" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
        {$iconPath}
    </g>
    <text x="400" y="660" text-anchor="middle" font-family="Arial, sans-serif" font-size="30" font-weight="700" fill="{$theme['color']}">{$theme['label']}</text>
</svg>
SVG;
    }
}
