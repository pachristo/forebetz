<x-filament-panels::page>
    <style>
        .seo-guide { color:#1a1f1c; line-height:1.45; font-size:15px; }
        .seo-guide h1 { font-size:1.6rem; color:#004421; margin:0 0 6px; }
        .seo-guide h2 { font-size:1.15rem; color:#004421; border-bottom:2px solid #BAAA00; padding-bottom:4px; margin:22px 0 10px; }
        .seo-guide h3 { font-size:1rem; color:#006531; margin:14px 0 6px; }
        .seo-guide p, .seo-guide li { margin:0 0 8px; }
        .seo-guide pre { margin:8px 0 14px; }
        .seo-guide .sub { color:#555; font-size:.95rem; margin-bottom:18px; }
        .seo-guide .box { background:#f4f7f5; border-left:4px solid #004421; padding:10px 12px; margin:10px 0 14px; }
        .seo-guide .warn { background:#fff8e6; border-left:4px solid #FCBD02; padding:10px 12px; margin:10px 0 14px; }
        .seo-guide .note { background:#eef7ff; border-left:4px solid #08f; padding:10px 12px; margin:10px 0 14px; }
        .seo-guide table { width:100%; border-collapse:collapse; margin:8px 0 16px; font-size:.92rem; }
        .seo-guide th, .seo-guide td { border:1px solid #cfd8d2; padding:7px 8px; text-align:left; vertical-align:top; }
        .seo-guide th { background:#004421; color:#fff; }
        .seo-guide tr:nth-child(even) td { background:#f7faf8; }
        .seo-guide ol.steps { padding-left:20px; }
        .seo-guide .mono { font-family:ui-monospace,Consolas,monospace; font-size:.88rem; background:#eee; padding:1px 4px; border-radius:3px; }
        .seo-guide .footer { margin-top:28px; padding-top:10px; border-top:1px solid #ccc; font-size:.85rem; color:#666; }
        .seo-guide .badge { display:inline-block; background:#14ae5c; color:#fff; font-size:.75rem; padding:2px 7px; border-radius:10px; font-weight:600; }
        .seo-guide .badge-auto { background:#ec221f; }
        .seo-guide a { color:#006531; text-decoration:underline; }
    </style>
    <div class="seo-guide max-w-none rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-white">
        @include('filament.pages.partials.seo-meta-guide-body')
    </div>
</x-filament-panels::page>
