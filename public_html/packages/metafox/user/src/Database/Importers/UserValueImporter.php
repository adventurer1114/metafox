<?php

namespace MetaFox\User\Database\Importers;

use MetaFox\Platform\Support\JsonImporter;
use MetaFox\User\Models\UserValue as Model;

/*
 * stub: packages/database/json-importer.stub
 */

class UserValueImporter extends JsonImporter
{
    // fill from data to model attributes.
    protected $fillable = [
        'name',
        'value',
        'default_value',
        'ordering',
    ];

    // fill from data to model refs.
    protected $relations = [
        'user',
    ];

    public function getModelClass(): string
    {
        return Model::class;
    }

}
