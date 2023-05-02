<?php

namespace MetaFox\Platform\Repositories\Contracts;

use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Interface AbstractRepositoryInterface
 * @package MetaFox\Platform\Repositories\Contracts
 */
interface AbstractRepositoryInterface
{
    /**
     * @param array<mixed> $params
     *
     * @return self
     */
    public function where(array $params): self;

    /**
     * @param array<int, mixed> $items
     *
     * @return bool
     * @throws ValidatorException
     */
    public function createMany(array $items): bool;
}
