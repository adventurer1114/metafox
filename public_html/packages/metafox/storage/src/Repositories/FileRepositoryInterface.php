<?php

namespace MetaFox\Storage\Repositories;

use Illuminate\Database\Eloquent\Collection;
use MetaFox\Storage\Models\StorageFile;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface File.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface FileRepositoryInterface
{
    /**
     * @param  mixed                   $originalId
     * @return Collection<StorageFile>
     */
    public function getByOriginalId(mixed $originalId): Collection;
}
