<?php

namespace MetaFox\Music\Database\Importers;

use MetaFox\Music\Models\PlaylistPrivacyStream;
use MetaFox\Platform\Support\JsonImporter;
use MetaFox\Music\Models\Playlist as Model;

/*
 * stub: packages/database/json-importer.stub
 */

class PlaylistImporter extends JsonImporter
{
    protected array $requiredColumns = ['user_id', 'owner_id'];

    public function getModelClass(): string
    {
        return Model::class;
    }

    public function afterPrepare(): void
    {
        $this->appendFileBundle('$image');
        $this->processPrivacyStream(PlaylistPrivacyStream::class);
    }

    public function processImport()
    {
        $this->remapRefs([
            '$owner', '$user',
            '$image.$id' => ['image_file_id'],
        ]);

        $this->processImportEntries();

        $this->upsertBatchEntriesInChunked(Model::class, ['id']);
    }

    public function processImportEntry(array &$entry): void
    {
        $oid = $entry['$oid'];

        $this->addEntryToBatch(
            Model::class,
            [
                'id'               => $oid,
                'user_id'          => $entry['user_id'] ?? null,
                'user_type'        => $entry['user_type'] ?? null,
                'owner_id'         => $entry['owner_id'] ?? $entry['user_id'],
                'owner_type'       => $entry['owner_type'] ?? $entry['user_type'],
                'total_track'      => $entry['total_track'] ?? 0,
                'total_length'     => $entry['total_length'] ?? 0,
                'total_like'       => $entry['total_like'] ?? 0,
                'total_comment'    => $entry['total_comment'] ?? 0,
                'total_reply'      => $entry['total_reply'] ?? 0,
                'total_share'      => $entry['total_share'] ?? 0,
                'total_view'       => $entry['total_view'] ?? 0,
                'total_play'       => $entry['total_play'] ?? 0,
                'total_attachment' => $entry['total_attachment'] ?? 0,
                'image_file_id'    => $entry['image_file_id'] ?? null,
                'name'             => html_entity_decode($entry['name'] ?? ''),
                'description'      => $this->parseText($entry['description'] ?? '', false),
                'ordering'         => $entry['ordering'] ?? 0,
                'is_active'        => $entry['is_active'] ?? true,
                'created_at'       => $entry['created_at'] ?? null,
                'updated_at'       => $entry['updated_at'] ?? null,
                'privacy'          => $this->privacyMapEntry($entry),
                'is_featured'      => $entry['is_featured'] ?? 0,
                'is_sponsor'       => $entry['is_sponsor'] ?? 0,
                'featured_at'      => $entry['featured_at'] ?? null,
                'sponsor_in_feed'  => $entry['sponsor_in_feed'] ?? 0,
            ]
        );
    }
}
