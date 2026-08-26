@props(['name', 'size' => 18])
@php($s = $size)
<svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" {{ $attributes }}>
@switch($name)
    @case('search')
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
        @break
    @case('cart')
        <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.5 2.5h3l2.7 12.4a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 2-1.6L23 6.5H6"/>
        @break
    @case('heart')
        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.6z"/>
        @break
    @case('favorite')
        <path d="M20.3 5.7a5.3 5.3 0 0 0-7.5 0L12 6.5l-.8-.8a5.3 5.3 0 0 0-7.5 7.5L12 21l8.3-7.8a5.3 5.3 0 0 0 0-7.5Z"/><path d="M17.5 7.5a2.7 2.7 0 0 1 .7 2.7" opacity=".55"/>
        @break
    @case('heart-fill')
        </svg><svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="currentColor" {{ $attributes }}><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.6z"/>
        @break
    @case('user')
        <path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/>
        @break
    @case('star')
        </svg><svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="currentColor" {{ $attributes }}><path d="M12 2l3.1 6.3 7 1-5 4.9 1.2 6.9L12 17.8 5.7 21.1l1.2-6.9-5-4.9 7-1z"/>
        @break
    @case('star-outline')
        <path d="M12 2l3.1 6.3 7 1-5 4.9 1.2 6.9L12 17.8 5.7 21.1l1.2-6.9-5-4.9 7-1z"/>
        @break
    @case('chevron-right')
        <path d="m9 18 6-6-6-6"/>
        @break
    @case('chevron-left')
        <path d="m15 18-6-6 6-6"/>
        @break
    @case('chevron-down')
        <path d="m6 9 6 6 6-6"/>
        @break
    @case('check')
        <path d="M20 6 9 17l-5-5"/>
        @break
    @case('x')
        <path d="M18 6 6 18M6 6l12 12"/>
        @break
    @case('mail')
        <rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 6.5 9 6.5 9-6.5"/>
        @break
    @case('phone')
        <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2Z"/>
        @break
    @case('lock')
        <rect x="4.5" y="10.5" width="15" height="10" rx="2"/><path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/>
        @break
    @case('eye')
        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
        @break
    @case('eye-off')
        <path d="M3 3l18 18"/><path d="M10.6 5.2A10.6 10.6 0 0 1 12 5c6.4 0 10 7 10 7a17.7 17.7 0 0 1-3.4 4.3M6.6 6.7C3.7 8.5 2 12 2 12s3.6 7 10 7a10.6 10.6 0 0 0 3.4-.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/>
        @break
    @case('plus')
        <path d="M12 5v14M5 12h14"/>
        @break
    @case('minus')
        <path d="M5 12h14"/>
        @break
    @case('filter')
        <line x1="4" y1="6" x2="20" y2="6"/><circle cx="9" cy="6" r="2" fill="var(--bg)"/><line x1="4" y1="12" x2="20" y2="12"/><circle cx="16" cy="12" r="2" fill="var(--bg)"/><line x1="4" y1="18" x2="20" y2="18"/><circle cx="11" cy="18" r="2" fill="var(--bg)"/>
        @break
    @case('grid')
        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
        @break
    @case('list')
        <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/>
        @break
    @case('truck')
        <rect x="1" y="6" width="14" height="11"/><path d="M15 9h4l3 4v4h-7z"/><circle cx="6" cy="19" r="1.6"/><circle cx="17.5" cy="19" r="1.6"/>
        @break
    @case('shield')
        <path d="M12 2 4 5v6c0 5 3.4 8.4 8 10 4.6-1.6 8-5 8-10V5z"/><path d="m9 12 2 2 4-4"/>
        @break
    @case('arrow-right')
        <path d="M5 12h14M13 6l6 6-6 6"/>
        @break
    @case('trash')
        <path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m2 0-1 14a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1L5 6"/>
        @break
    @case('compare')
        <path d="M7 3v15M7 3 3.5 6.5M7 3l3.5 3.5"/><path d="M17 21V6M17 21l-3.5-3.5M17 21l3.5-3.5"/><path d="M3 21h8M13 3h8"/>
        @break
    @case('bell')
        <path d="M6 8a6 6 0 0 1 12 0c0 4 1.5 5.5 2 6H4c.5-.5 2-2 2-6Z"/><path d="M9.5 18a2.5 2.5 0 0 0 5 0"/>
        @break
    @case('package')
        <path d="m3.5 7.5 8.5-4 8.5 4-8.5 4-8.5-4Z"/><path d="M3.5 7.5v9l8.5 4 8.5-4v-9"/><path d="M12 11.5v9"/>
        @break
    @case('money')
        <path d="M12 2v20M17 6.5c0-2-2-3-5-3s-5 1.2-5 3 2 2.6 5 3 5 1 5 3-2 3-5 3-5-1-5-3"/>
        @break
    @case('trending-up')
        <path d="M3 17 9 11l4 4 8-8"/><path d="M15 7h6v6"/>
        @break
    @case('users')
        <path d="M16 21a4 4 0 0 0-8 0"/><circle cx="12" cy="11" r="4"/><path d="M22 21a4 4 0 0 0-3-3.9M17.5 3.5a4 4 0 0 1 0 7.7"/>
        @break
    @case('store')
        <path d="M3 9 4 4h16l1 5"/><path d="M4 9v10a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V9"/><path d="M9 20v-6h6v6"/>
        @break
    @case('settings')
        <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>
        @break
    @case('dashboard')
        <rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>
        @break
    @case('image')
        <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/>
        @break
    @case('upload')
        <path d="M12 16V4M6 10l6-6 6 6"/><path d="M4 20h16"/>
        @break
    @case('moon')
        <path d="M20.8 13.7A8.5 8.5 0 1 1 10.3 3.2a7 7 0 0 0 10.5 10.5Z"/>
        @break
    @case('sun')
        <circle cx="12" cy="12" r="4.5"/><path d="M12 2v2.5M12 19.5V22M4.2 4.2l1.8 1.8M18 18l1.8 1.8M2 12h2.5M19.5 12H22M4.2 19.8 6 18M18 6l1.8-1.8"/>
        @break
    @case('globe')
        <circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>
        @break
    @case('chevron-up')
        <path d="m18 15-6-6-6 6"/>
        @break
    @case('pen')
        <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z"/>
        @break
    @case('briefcase')
        <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
        @break
    @case('palette')
        <circle cx="12" cy="12" r="9"/><circle cx="8" cy="11" r="1.3" fill="currentColor"/><circle cx="11" cy="8" r="1.3" fill="currentColor"/><circle cx="15" cy="8.5" r="1.3" fill="currentColor"/><circle cx="16" cy="12.5" r="1.3" fill="currentColor"/>
        @break
    @case('box')
        <rect x="3" y="8" width="18" height="13" rx="1.5"/><path d="M3 8 12 3l9 5"/><path d="M12 12v9"/>
        @break
    @case('book')
        <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v17H6.5A2.5 2.5 0 0 0 4 21.5Z"/><path d="M4 4.5v17"/>
        @break
    @case('cpu')
        <rect x="6" y="6" width="12" height="12" rx="1.5"/><rect x="9.5" y="9.5" width="5" height="5"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>
        @break
    @case('pencil')
        <path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3Z"/>
        @break
    @case('notebook')
        <rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M8 3v18"/><path d="M4 7.5h2M4 12h2M4 16.5h2"/>
        @break
    @case('marker')
        <path d="M8 21 3 16l9-9 5 5Z"/><path d="M12 7l5 5"/><path d="M15 4l5 5-3 3-5-5Z"/>
        @break
    @case('eraser')
        <path d="M3 16 13 6l6 6-8 8H7Z"/><path d="M7 20 3 16"/>
        @break
    @case('glue')
        <path d="M9 2h6v4l2 2v14H7V8l2-2Z"/><path d="M9 12h6"/>
        @break
    @case('ruler')
        <rect x="2" y="7" width="20" height="8" rx="1"/><path d="M6 7v3M10 7v2M14 7v3M18 7v2"/>
        @break
    @case('folder')
        <path d="M3 6.5A1.5 1.5 0 0 1 4.5 5H10l2 2h7.5A1.5 1.5 0 0 1 21 8.5v10A1.5 1.5 0 0 1 19.5 20h-15A1.5 1.5 0 0 1 3 18.5Z"/>
        @break
    @case('tray')
        <path d="M3 15 6 5h12l3 10"/><path d="M3 15h4l2 3h6l2-3h4v4.5a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Z"/>
        @break
    @case('stapler')
        <path d="M3 17 5 7h13l3 10"/><path d="M2 17h20v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1Z"/><path d="M9 7 11 3h2l2 4"/>
        @break
    @case('calculator')
        <rect x="4" y="2" width="16" height="20" rx="2"/><path d="M8 6h8"/><circle cx="8" cy="11" r="1"/><circle cx="12" cy="11" r="1"/><circle cx="16" cy="11" r="1"/><circle cx="8" cy="15" r="1"/><circle cx="12" cy="15" r="1"/><circle cx="16" cy="15" r="1"/><circle cx="8" cy="19" r="1"/><circle cx="12" cy="19" r="1"/><circle cx="16" cy="19" r="1"/>
        @break
    @case('id-card')
        <rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="8" cy="12" r="2"/><path d="M4.5 17a4 3 0 0 1 7 0"/><path d="M14 9h5M14 13h5"/>
        @break
    @case('stamp')
        <path d="M9 3h6v4l2 2v2H7v-2l2-2Z"/><path d="M4 21h16"/><rect x="6" y="17" width="12" height="4"/>
        @break
    @case('brush')
        <path d="M6 20a3 3 0 0 1 0-6c1 0 2 .8 2 2v2a2 2 0 0 1-2 2Z"/><path d="M9 15 15 3l2 2-6 12"/>
        @break
    @case('clay')
        <circle cx="12" cy="12" r="3.5"/><circle cx="6" cy="7" r="2.5"/><circle cx="18" cy="7" r="2.5"/><circle cx="6" cy="18" r="2.5"/><circle cx="18" cy="18" r="2.5"/>
        @break
    @case('scissors')
        <circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4 8.1 15.9"/><path d="m14.8 14.8 5.2 5.2"/><path d="M8.1 8.1 12 12"/>
        @break
    @case('origami')
        <path d="M12 2 3 20h18Z"/><path d="M12 2v18"/><path d="m7.5 13 4.5-4 4.5 4"/>
        @break
    @case('scrapbook')
        <rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/><circle cx="8" cy="14" r="1.5"/><path d="m12 16.5 3-3.5 4 5"/>
        @break
    @case('bag')
        <path d="M6 8h12l1.5 13a1 1 0 0 1-1 1.1H5.5A1 1 0 0 1 4.5 21Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>
        @break
    @case('gift')
        <rect x="3" y="8" width="18" height="4" rx="1"/><rect x="4" y="12" width="16" height="9" rx="1"/><path d="M12 8v13"/><path d="M12 8a3 3 0 1 1-3-4c1.7 0 3 1.8 3 4Z"/><path d="M12 8a3 3 0 1 0 3-4c-1.7 0-3 1.8-3 4Z"/>
        @break
    @case('paper-roll')
        <ellipse cx="12" cy="5" rx="4" ry="2.5"/><path d="M8 5v13"/><path d="M16 5v13"/><ellipse cx="12" cy="18" rx="4" ry="2.5"/>
        @break
    @case('ribbon')
        <circle cx="12" cy="6" r="3.5"/><path d="M7 21l5-7 5 7-2-9.5-3 2-3-2Z"/>
        @break
    @case('tape')
        <circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/>
        @break
    @case('tag')
        <path d="M12.6 2.4 20 9.8a2 2 0 0 1 0 2.8l-7.4 7.4a2 2 0 0 1-2.8 0L2.4 12.6A2 2 0 0 1 2 11.2V4a2 2 0 0 1 2-2h8.6Z"/><circle cx="7.5" cy="7.5" r="1.3" fill="currentColor"/>
        @break
    @case('planner')
        <rect x="4" y="4" width="16" height="17" rx="1.5"/><path d="M4 9h16"/><path d="M8 2v4M16 2v4"/><path d="M8 13h3M8 17h5"/>
        @break
    @case('notepad')
        <path d="M6 3h9l5 5v13H6Z"/><path d="M15 3v5h5"/><path d="M9 12h6M9 16h6"/>
        @break
    @case('book-cover')
        <rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M4 7h16"/><circle cx="12" cy="14" r="2.5"/>
        @break
    @case('printer')
        <path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1.5"/><path d="M6 14h12v7H6Z"/>
        @break
    @case('calendar')
        <rect x="3" y="4.5" width="18" height="16.5" rx="2"/><path d="M3 9.5h18"/><path d="M8 2.5v4M16 2.5v4"/><circle cx="8" cy="14" r="1"/><circle cx="12" cy="14" r="1"/><circle cx="16" cy="14" r="1"/>
        @break
    @case('poster')
        <rect x="4" y="2" width="16" height="20" rx="1.5"/><path d="M8 7h8M8 11h8M8 15h5"/>
        @break
    @case('usb')
        <rect x="9" y="2" width="6" height="7" rx="1"/><path d="M9 4H7M17 4h-2"/><path d="M8 9h8v10a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2Z"/>
        @break
    @case('cable')
        <path d="M9 3 3 9l4 4"/><path d="m15 21 6-6-4-4"/><path d="M8 13a5 5 0 0 0 7 0l1-1a5 5 0 0 0 0-7l-1-1"/>
        @break
    @case('headphones')
        <path d="M3 14v-2a9 9 0 0 1 18 0v2"/><rect x="2" y="14" width="5" height="7" rx="1.5"/><rect x="17" y="14" width="5" height="7" rx="1.5"/>
        @break
    @case('mouse')
        <rect x="7" y="2" width="10" height="20" rx="5"/><path d="M12 2v7"/>
        @break
    @case('battery')
        <rect x="2" y="7" width="18" height="10" rx="2"/><path d="M22 10v4"/><path d="M6 10v4"/>
        @break
    @case('sketchbook')
        <rect x="4" y="2" width="16" height="20" rx="1.5"/><path d="M8 8 12 13l3-2 3 4"/><circle cx="9" cy="7" r="1.3" fill="currentColor"/>
        @break
    @default
        <circle cx="12" cy="12" r="9"/>
@endswitch
</svg>
