<?php

namespace MetaFox\Core\Listeners;

use MetaFox\Core\Support\Link\FetchLink;
use MetaFox\Core\Support\Link\Providers\Vimeo;

class ParseVimeoUrlListener
{
    /**
     * @param string $url
     *
     * @return ?array<mixed>
     */
    public function handle(string $url): ?array
    {
        // TODO: should have a place in BE to configure these API credentials
        $service = new Vimeo([
            'client_id' => '2b4d6aa1d9e64f18eef3df4b5e4e547376e6f04e',
            'client_secret' => 'FixTTTtTzU4OpxBzbULQKaATzevHJhI9EYDncG+mUB6efJ9dk89qb+8Z88YwaJakjimc7inhpj4PLtw//Ro5A3i8/JwGuHGyYMULlKAtC71N40BNKhSTuH/QDmPfz3HQ',
            'access_token' => '522982a1499779d97f858c7e89228ffa',
        ]);

        return (new FetchLink($service))->parse($url);
    }
}
