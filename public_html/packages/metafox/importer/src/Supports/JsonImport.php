<?php

namespace MetaFox\Importer\Supports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use InvalidArgumentException;
use MetaFox\Core\Models\SiteSetting;
use MetaFox\Platform\Support\JsonImporter;
use MetaFox\Storage\Database\Importers\FileSystemImporter;
use MetaFox\Storage\Database\Importers\StorageFileImporter;
use MetaFox\Storage\Models\StorageFile;

class JsonImport
{
    public static function getMorphedModel(string|null $resourceName): mixed
    {
        if ($resourceName == 'file') {
            return StorageFile::class;
        }
        if ($resourceName == 'core_storage') {
            return SiteSetting::class;
        }

        $modelClass = Relation::getMorphedModel($resourceName);

        if (!$modelClass) {
            throw new InvalidArgumentException("Failed getting relation map of $resourceName.");
        }

        return $modelClass;
    }

    public static function getModelForResource(string $resourceName): Model
    {
        return resolve(static::getMorphedModel($resourceName));
    }

    public static function newJsonImporter(?string $resourceName): JsonImporter
    {
        if ($resourceName == 'file') {
            return new StorageFileImporter();
        }
        if ($resourceName == 'core_storage') {
            return new FileSystemImporter();
        }

        $modelClass = static::getMorphedModel($resourceName);

        if (method_exists($modelClass, 'newImporter')) {
            return $modelClass->newImporter();
        }

        $class = str_replace('\\Models\\', '\\Database\\Importers\\', $modelClass) . 'Importer';

        if (class_exists($class)) {
            $importer = resolve($class);

            if ($importer instanceof JsonImporter) {
                return $importer;
            }
        }

        throw new InvalidArgumentException("Failed getting json importer for $resourceName. class $class not found.");
    }
}
