<?php

namespace MetaFox\StaticPage\Listeners;

use MetaFox\StaticPage\Models\StaticPage;

class ParseToRouteListener
{
    /**
     * @param string $url
     *
     * @return array<string,mixed>|null|void
     */
    public function handle(string $url)
    {
        try {
            /** @var StaticPage $page */
            $id = StaticPage::query()->where('slug', '=', $url)->value('id');

            if (!$id) {
                return null;
            }

            return [
                'path' => '/static-page/' . $id,
            ];
        } catch (\Exception $exception) {
            // do nothing
        }
    }
}
