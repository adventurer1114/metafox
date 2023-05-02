<?php

namespace MetaFox\Core\Listeners;

use MetaFox\Core\Support\Link\FetchLink;
use MetaFox\Core\Support\Link\Providers\Instagram;

class ParseInstagramUrlListener
{
    /**
     * @param string $url
     *
     * @return ?array<mixed>
     */
    public function handle(string $url): ?array
    {
        // TODO: should have a place in BE to configure these API credentials
        $service = new Instagram([
            'app_id' => '571530043470390',
            'app_secret' => '163dcb5dea44c4c365b9695b194ff6cb',
        ]);

        return (new FetchLink($service))->parse($url);
    }
}
