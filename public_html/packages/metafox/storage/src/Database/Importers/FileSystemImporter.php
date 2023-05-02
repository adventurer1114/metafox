<?php

namespace MetaFox\Storage\Database\Importers;

use MetaFox\Core\Models\SiteSetting as Model;
use MetaFox\Platform\Support\JsonImporter;
use MetaFox\Storage\Http\Requests\v1\Disk\Admin\UpdateFtpDiskRequest;
use MetaFox\Storage\Http\Requests\v1\Disk\Admin\UpdateS3DiskRequest;
use MetaFox\Storage\Http\Requests\v1\Disk\Admin\UpdateSftpDiskRequest;

/*
 * stub: packages/database/json-importer.stub
 */

class FileSystemImporter extends JsonImporter
{
    public function processImport()
    {
        $this->processImportEntries();
        $this->upsertBatchEntriesInChunked(Model::class, ['id']);
    }

    protected function processImportEntry(array &$entry): void
    {
        $name       = sprintf('storage.filesystems.disks.%s', $this->bundle?->source . '_' . $entry['service_id']);
        $configName = sprintf('filesystems.disks.%s', $this->bundle?->source . '_' . $entry['service_id']);
        $config     = $this->mappingFileSystemAttr($entry['service_id'], $entry['config']);
        $this->addEntryToBatch(Model::class, [
            'id'            => $entry['$oid'],
            'module_id'     => 'storage',
            'package_id'    => 'metafox/storage',
            'value_actual'  => json_encode($config),
            'value_default' => json_encode($config),
            'type'          => 'array',
            'config_name'   => $configName,
            'name'          => $name,
            'is_public'     => 0,
        ]);
    }

    private function mappingFileSystemAttr($serviceId, $config)
    {
        if (!is_array($config)) {
            $config = json_decode($config, true);
        }
        $rules = match ($serviceId) {
            's3', 'dospace', 's3compatible' => (new UpdateS3DiskRequest())->rules(),
            'ftp'   => (new UpdateFtpDiskRequest())->rules(),
            'sftp'  => (new UpdateSftpDiskRequest())->rules(),
            default => [],
        };
        $result = [];
        foreach ($rules as $key => $rule) {
            switch ($key) {
                case 'url':
                    if (isset($config['cloudfront_url'])) {
                        $result[$key] = $config['cloudfront_url'];
                    } elseif (isset($config['base_url'])) {
                        $result[$key] = $config['base_url'];
                    }
                    break;
                case 'root':
                    if (isset($config['base_path'])) {
                        $result[$key] = $config['base_path'];
                    }
                    break;
                case 'use_path_style_endpoint':
                    if (isset($config['cloudfront_enabled'])) {
                        $result[$key] = (bool) $config['cloudfront_enabled'];
                    }
                    break;
                case 'timeout':
                    if (isset($config['timeout'])) {
                        $result[$key] = (int) $config['timeout'];
                    } else {
                        $result[$key] = 60; // Default timeout
                    }
                    break;
                default:
                    if (isset($config[$key])) {
                        $result[$key] = $config[$key];
                    } else {
                        [, $type] = explode('|', $rule);
                        match ($type) {
                            'string'  => $result[$key] = '',
                            'int'     => $result[$key] = 0,
                            'boolean' => $result[$key] = false,
                            default   => $result[$key] = null
                        };
                    }
                    break;
            }
        }

        $result['driver'] = 'local';

        return $result;
    }
}
