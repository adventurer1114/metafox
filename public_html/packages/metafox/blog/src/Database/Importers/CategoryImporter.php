<?php

namespace MetaFox\Blog\Database\Importers;

use MetaFox\Blog\Models\Category as Model;
use MetaFox\Platform\Support\JsonImporterForCategory;

/*
 * stub: packages/database/json-importer.stub
 */

class CategoryImporter extends JsonImporterForCategory
{

    public function getModelClass(): string
    {
        return Model::class;
    }
}
