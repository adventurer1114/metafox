<?php

namespace MetaFox\Core\Listeners;

use MetaFox\Core\Support\Link\FetchLink;
use MetaFox\Core\Support\Link\Providers\Youtube;

class ParseYouTubeUrlListener
{
    /**
     * @param string $url
     *
     * @return ?array<mixed>
     */
    public function handle(string $url): ?array
    {
        // TODO: should have a place in BE to configure these API credentials
        $service = new Youtube([
            'api_key' => 'AIzaSyA-pIQldPRcIDyKk_xe5Fl9YIkGhF-B7os',
        ]);

        return (new FetchLink($service))->parse($url);
    }
}
