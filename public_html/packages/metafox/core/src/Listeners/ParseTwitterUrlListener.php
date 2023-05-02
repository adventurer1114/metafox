<?php

namespace MetaFox\Core\Listeners;

use MetaFox\Core\Support\Link\FetchLink;
use MetaFox\Core\Support\Link\Providers\Twitter;

class ParseTwitterUrlListener
{
    /**
     * @param string $url
     *
     * @return ?array<mixed>
     */
    public function handle(string $url): ?array
    {
        // TODO: should have a place in BE to configure these API credentials
        $service = new Twitter([
            'api_key' => 'kHpFx7kigbz8htvQ3cPc9PXqC',
            'secret_key' => '21W7tzWW3AfkVnQjXmlvJhDxaTaxVKAR0BHHqJMzbGKCQB8GS4',
        ]);

        return (new FetchLink($service))->parse($url);
    }
}
