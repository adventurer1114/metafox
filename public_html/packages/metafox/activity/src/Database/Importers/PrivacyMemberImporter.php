<?php

namespace MetaFox\Activity\Database\Importers;

use MetaFox\Activity\Models\PrivacyMember as Model;
use MetaFox\Platform\Support\JsonImporter;

class PrivacyMemberImporter extends JsonImporter
{
    protected array $requiredColumns = ['user_id'];

    public function getModelClass(): string
    {
        return Model::class;
    }

    // batch to raw query in database.
    public function processImport()
    {
        $this->remapRefs([
            '$user' => ['user_id', 'user_type'],
            '$privacy',
        ]);

        $this->processImportEntries();
        $this->upsertBatchEntriesInChunked(Model::class, ['id']);
    }

    public function processImportEntry(array &$entry): void
    {
        $oid = $entry['$oid'];

        $this->addEntryToBatch(Model::class, [
            'id'         => $oid,
            'user_id'    => $entry['user_id'],
            'privacy_id' => $entry['privacy_id'] ?? $entry['default_privacy_id'],
        ]);
    }
}
