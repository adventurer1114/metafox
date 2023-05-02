<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($items as $item)
        <url>
            <loc>{{$item['url']}}</loc>
            @if($item['lastmod']??null)
                <lastmod>{{$item['lastmod']}}</lastmod>
            @endif
        </url>
    @endforeach
</urlset>