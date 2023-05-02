<?php

namespace MetaFox\Core\Listeners;

class ParseUrlListener
{
    /**
     * @param string $url
     *
     * @return ?array<mixed>
     */
    public function handle(string $url): ?array
    {
        $data  = app('events')->dispatch('core.process_parse_url', [$url], true);
        if (empty($data)) {
            $data  = app('events')->dispatch('core.after_parse_url', [$url], true);
        }

        return $data;
    }
}
