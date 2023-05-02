<?php

namespace MetaFox\Localize\Contracts;

use MetaFox\Localize\Models\Timezone as Model;

/**
 * Interface TimezoneSupportContract.
 */
interface TimezoneSupportContract
{
    /**
     * @return array<int, Model>
     */
    public function getTimezones(): array;

    /**
     * @param int $timezoneId
     *
     * @return Model|null
     */
    public function getTimezone(int $timezoneId): ?Model;

    /**
     * @return array<string, Model>
     */
    public function getAllActiveTimezones(): array;

    /**
     * @return array<mixed>
     */
    public function getActiveOptions();

    /**
     * @return ?string
     */
    public function getName(?int $id);

    /**
     * @param  string     $name
     * @return Model|null
     */
    public function getTimezoneByName(?string $name): ?Model;

    /**
     * @return int
     */
    public function getDefaultTimezoneId(): int;
}
