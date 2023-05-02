<?php

namespace MetaFox\SEO\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\ModuleManager;

class SitemapController extends Controller
{
    public const PER_PAGE = 500;

    /**
     * @return Response
     */
    public function index(): Response
    {
        // add form settings to manage site map exclude types.
        $excludeTypes = Settings::get('seo.sitemap_exclude_types', []);
        $items        = new \ArrayObject();

        // scan types for models.
        $data  = ModuleManager::instance()->discoverSettings('getSitemap');
        $types = Arr::collapse(array_values($data));

        foreach ($types as $type) {
            // skip by seo settings.
            if (in_array($type, $excludeTypes)) {
                continue;
            }

            $modelClass = Relation::getMorphedModel($type);
            if (!$modelClass || !class_exists($modelClass)) {
                continue;
            }

            $total = $modelClass::count();
            $limit = max(1, ceil($total / static::PER_PAGE)); // 500 item per site map url.
            /** @var Model $modelInstance */
            $modelInstance = resolve($modelClass);
            $lastMod       = null;

            if (!method_exists($modelInstance, 'toUrl')) {
                continue;
            }

            if ($total == 0) {
                continue;
            }

            if (in_array('updated_at', $modelInstance->getFillable())) {
                $lastMod = DB::table($modelInstance->getTable())->max('updated_at');

                if ($lastMod) {
                    $lastMod = Carbon::create($lastMod)->tz('UTC')->toAtomString();
                }
            }

            for ($page = 0; $page < $limit; $page++) {
                $items[] = [
                    'url' => $page > 0 ?
                        sprintf('%s/sitemap/%s-%s.xml', config('app.url'), $type, $page) :
                        sprintf('%s/sitemap/%s.xml', config('app.url'), $type),
                    'lastmod' => $lastMod,
                ];
            }
        }

        // allow add others hooks.
        app('events')->dispatch('seo.sitemap_index', $items);
        $html = view('seo::sitemap.index', ['items' => $items])->render();

        return response($html)->withHeaders(['Content-Type' => 'text/xml']);
    }

    /**
     * @param  string   $type
     * @param  int|null $page
     * @return Response
     */
    public function urls(string $type, ?int $page = 0): Response
    {
        $headers       = ['Content-Type' => 'text/xml'];
        $emptyResponse = view('seo::sitemap.urls', ['items' => []])->render();
        $items         = new \ArrayObject();
        $modelClass    = Relation::getMorphedModel($type);
        if (!$modelClass || !class_exists($modelClass)) {
            return response($emptyResponse)->withHeaders($headers);
        }

        /** @var Model $modelInstance */
        $modelInstance = resolve($modelClass);

        if (!$modelInstance instanceof Model) {
            return response($emptyResponse)->withHeaders($headers);
        }

        $rows = $modelInstance->newQuery()->forPage(++$page, static::PER_PAGE)->cursor();

        foreach ($rows as $row) {
            $lastMod = $row->updated_at;

            if (!method_exists($row, 'toUrl')) {
                continue;
            }

            $url = $row?->toUrl();
            if (!$url) {
                continue;
            }

            $items[] = [
                'url'     => $url,
                'lastmod' => $lastMod ? Carbon::create($lastMod)->tz('UTC')->toAtomString() : null,
            ];
        }

        $content = view('seo::sitemap.urls', ['items' => $items])->render();

        return response($content)->withHeaders($headers);
    }
}
