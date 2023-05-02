<?php

namespace MetaFox\ActivityPoint\Database\Importers;

use MetaFox\ActivityPoint\Repositories\PointSettingRepositoryInterface;
use MetaFox\Platform\Support\JsonImporter;
use MetaFox\ActivityPoint\Models\PointSetting as Model;

/*
 * stub: packages/database/json-importer.stub
 */

class PointSettingImporter extends JsonImporter
{
    protected array $requiredColumns = ['role_id', 'name'];

    private array $aptSettings;

    public function __construct()
    {
        $this->aptSettings = $this->mapOldData();
    }

    public function getModelClass(): string
    {
        return Model::class;
    }

    public function processImport()
    {
        $this->remapRefs(['$role']);

        $this->processImportEntries();

        $this->upsertBatchEntriesInChunked(Model::class, ['role_id', 'name']);
    }

    public function processImportEntry(array &$entry): void
    {
        $oldData = $this->getOldData($entry);
        if (!$oldData) {
            return;
        }

        $this->addEntryToBatch(Model::class, [
            'id'         => $entry['$oid'],
            'role_id'    => $entry['role_id'],
            'name'       => $entry['name'],
            'action'     => $oldData['action'] ?? '',
            'is_active'  => $entry['is_active'] ?? 1,
            'points'     => $entry['points'] ?? 0,
            'max_earned' => $entry['max_earned'] ?? 0,
            'period'     => $entry['period'] ?? 0,
        ]);
    }

    private function getKeySetting($setting): string
    {
        return $setting['role_id'] . '.' . $setting['name'];
    }

    private function getOldData(array $entry): ?array
    {
        $key = $this->getKeySetting($entry);

        if (!isset($this->aptSettings[$key])) {
            return null;
        }

        return $this->aptSettings[$key];
    }

    private function mapOldData(): array
    {
        $settings = resolve(PointSettingRepositoryInterface::class)
            ->getAllPointSetting()->toArray();

        $maps = [];

        foreach ($settings as $setting) {
            $key = $this->getKeySetting($setting);

            $maps[$key] = [
                'id'      => $setting['id'],
                'role_id' => $setting['role_id'],
                'action'  => $setting['action'],
            ];
        }

        return $maps;
    }
}
