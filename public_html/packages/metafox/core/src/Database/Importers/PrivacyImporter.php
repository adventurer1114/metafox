<?php

namespace MetaFox\Core\Database\Importers;

use MetaFox\Core\Models\Privacy as Model;
use MetaFox\Platform\Support\JsonImporter;

class PrivacyImporter extends JsonImporter
{
    protected array $requiredColumns = ['privacy', 'privacy_type', 'user_id', 'owner_id'];

    public function getModelClass(): string
    {
        return Model::class;
    }

    // batch to raw query in database.
    public function processImport()
    {
        $this->remapRefs([
            '$owner' => ['owner_id', 'owner_type'],
            '$user'  => ['user_id', 'user_type'],
            '$item'  => ['item_id', 'item_type'],
        ]);

        $this->processImportEntries();
        $this->upsertBatchEntriesInChunked(Model::class, ['privacy_id']);
    }

    public function processImportEntry(array &$entry): void
    {
        $oid = $entry['$oid'];

        $this->addEntryToBatch(Model::class, [
            'privacy_id'   => $oid,
            'item_id'      => $entry['item_id'],
            'item_type'    => $entry['item_type'],
            'owner_id'     => $entry['owner_id'],
            'owner_type'   => $entry['owner_type'],
            'privacy'      => $entry['privacy'],
            'privacy_type' => $entry['privacy_type'],
            'user_id'      => $entry['user_id'],
            'user_type'    => $entry['user_type'],
        ]);
    }
}
