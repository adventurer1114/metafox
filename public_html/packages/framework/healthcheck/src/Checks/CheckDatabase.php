<?php

namespace MetaFox\HealthCheck\Checks;

use Illuminate\Support\Facades\DB;
use MetaFox\Platform\HealthCheck\Checker;
use MetaFox\Platform\HealthCheck\Result;
use MetaFox\Platform\Support\DbTableHelper;

class CheckDatabase extends Checker
{
    public function check(): Result
    {
        $result = $this->makeResult();

        try {
            $result->success(sprintf('Database connection: %s', DB::getDriverName()));
            $version = DbTableHelper::getDriverVersion();
            $dbSize  = DbTableHelper::getDatabaseSize();

            $result->success(sprintf('Database driver version: %s', $version));
            $result->success(sprintf('Database size: %s', human_readable_bytes($dbSize)));
        } catch (\Exception $exception) {
            $result->error('Coult not connect to database ' . $exception->getMessage());
        }

        return $result;
    }

    public function getName()
    {
        return 'Database';
    }
}
