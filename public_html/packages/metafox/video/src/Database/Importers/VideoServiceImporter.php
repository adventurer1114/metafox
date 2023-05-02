<?php

namespace MetaFox\Video\Database\Importers;

use MetaFox\Platform\Support\JsonImporter;
use MetaFox\Video\Models\VideoService as Model;

/*
 * stub: packages/database/json-importer.stub
 */

class VideoServiceImporter extends JsonImporter
{
    // fill from data to model attributes.
    protected $fillable = [
        'is_default',
        'is_active',
        'driver',
        'name',
        'service_class',
        'extra',
        'created_at',
        'updated_at',
    ];

    // fill from data to model refs.
    protected $relations = [];

    public function getModelClass(): string
    {
        return Model::class;
    }

    public function saving($model, array $data, array $relations, string $source): void
    {
        if (!$model instanceof Model) {
            throw new \RuntimeException(sprintf('Failed importing data %s', __CLASS__));
        }

        // handle saving logic
    }

    public function saved($model, array $data, array $relations, string $source): void
    {
        if (!$model instanceof Model) {
            throw new \RuntimeException(sprintf('Failed importing data %s', __CLASS__));
        }

        // handle saved logic
    }
}
