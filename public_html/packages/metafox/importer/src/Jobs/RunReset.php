<?php

namespace MetaFox\Importer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use MetaFox\Importer\Repositories\BundleRepositoryInterface;

/**
 * stub: packages/jobs/job-queued.stub.
 * @code
 * \MetaFox\Importer\Jobs\RunReset::dispatchSync()
 * @endcode
 */
class RunReset implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    private BundleRepositoryInterface $bundleRepository;


    private ?array $filter;

    /**
     * @param  array|null  $filter
     */
    public function __construct(?array $filter)
    {
        $this->filter = $filter;
    }


    public function uniqueId(): string
    {
        return __CLASS__;
    }

    public function handle(): void
    {
        $this->bundleRepository = resolve(BundleRepositoryInterface::class);
        $filename = 'storage/app/importer/schedule.json';
        $this->bundleRepository->importScheduleJson($filename, $this->filter);

        $this->bundleRepository->addLockFile();

        // clean queue

        ImportMonitor::dispatch();
    }

    public function fail(\Exception $exception = null): void
    {
        Log::channel('importer')
            ->error(sprintf('Error handle %s: %s', __METHOD__, $exception?->getMessage()));
    }
}
