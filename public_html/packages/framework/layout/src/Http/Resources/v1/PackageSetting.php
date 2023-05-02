<?php

namespace MetaFox\Layout\Http\Resources\v1;

use MetaFox\Layout\Models\Theme;
use MetaFox\Layout\Models\Variant;

class PackageSetting
{

    private function getVariants(): array
    {
        $themes = Theme::query()
            ->where('resolution',  'web')
            ->where('is_active',  1)
            ->pluck('theme_id')
            ->toArray();

        $result = [];
        /** @var Variant[] $rows */
        $rows = Variant::query()
            ->whereIn('theme_id', $themes)
            ->where('is_active',  1)
            ->get();

        foreach ($rows as $row) {
            $result[] = [
                'id'    => sprintf('%s:%s', $row->theme_id, $row->variant_id),
                'title' => $row->title,
                "image" => sprintf('https://metafox-dev.s3.amazonaws.com/kl/themes/%s/%s.png?v=1', $row->theme_id,
                    $row->variant_id),
            ];
        }

        return $result;
    }

    public function getWebSettings(): array
    {
        return [
            'variants' => $this->getVariants(),
        ];
    }
}
