<?php

namespace MetaFox\HealthCheck\Checks;


use MetaFox\Platform\HealthCheck\Checker;
use MetaFox\Platform\HealthCheck\Result;

class CheckServerLoad extends Checker
{
    public function check(): Result
    {
        $result = $this->makeResult();

        $loadtime = sys_getloadavg();

        if (!$loadtime) {
            $result->error('Failed geting system load average, sys_getloadavg()');
        } elseif ($loadtime[0] > 1) {
            $result->warn(sprintf('Server load is high: %.2f, %2.f, %.2f', $loadtime[0], $loadtime[1], $loadtime[2]));
        } else {
            $result->success(sprintf('Load Avg: %.2f, %2.f, %.2f', $loadtime[0], $loadtime[1], $loadtime[2]));
        }

        $this->checkMemoryUsage($result);
        $this->checkDiskspace($result);
        return $result;
    }

    public function checkMemoryUsage(Result $result)
    {
        memory_get_peak_usage();

        memory_get_usage();

        $result->success(sprintf("Memory usage %s, memory peak usage %s",
            human_readable_bytes(memory_get_usage()),
            human_readable_bytes(memory_get_peak_usage())));

    }

    public function checkDiskspace(Result $result)
    {

        $root = base_path();
        $freeSpace = disk_free_space($root);
        $totalSpace = disk_total_space($root);
        $percent = sprintf('%.1f%%', $freeSpace / $totalSpace * 100);

        $result->success(
            sprintf(
                'Disk free space %s - %s avaiable of total %s',
                $percent,
                human_readable_bytes($freeSpace),
                human_readable_bytes($totalSpace)
            )
        );
    }

    public function getName()
    {
        return 'Server Status';
    }
}