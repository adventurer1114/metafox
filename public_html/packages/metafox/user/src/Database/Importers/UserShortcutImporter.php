<?php

namespace MetaFox\User\Database\Importers;

use MetaFox\Platform\Support\JsonImporter;
use MetaFox\User\Models\UserShortcut as Model;

/*
 * stub: packages/database/json-importer.stub
 */

class UserShortcutImporter extends JsonImporter
{
    // fill from data to model attributes.
    protected $fillable = [
        'sort_type',
        'updated_at',
        'created_at',
    ];

    // fill from data to model refs.
    protected $relations = [
        'user',
        'item',
    ];

    public function getModelClass(): string
    {
        return Model::class;
    }

}
