<?php

namespace MetaFox\Video\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use MetaFox\Storage\Models\StorageFile;
use MetaFox\Video\Contracts\ProviderManagerInterface;
use MetaFox\Video\Repositories\VideoRepositoryInterface;

/**
 * Class VideoEncodingJob.
 */
class VideoProcessingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private StorageFile $file;

    /**
     * 5 minutes timeouts for this job.
     * @var int
     */
    public $timeout = 300;

    private int $videoId;

    /**
     * @var array<string,mixed>
     */
    protected array $extra = [];

    /**
     * Create a new job instance.
     *
     * @param StorageFile          $file
     * @param int                  $videoId
     * @param array<string, mixed> $attributes
     */
    public function __construct(StorageFile $file, int $videoId, array $extra = [])
    {
        $this->file    = $file;
        $this->videoId = $videoId;
        $this->extra   = $extra;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            $provider   = resolve(ProviderManagerInterface::class);
            $repository = resolve(VideoRepositoryInterface::class);

            $service = $provider->getDefaultServiceClass();
            $data    = $service->processVideo($this->file);

            if (!empty($data)) {
                $repository->doneProcessVideo($this->videoId, array_merge($data, $this->extra));

                // Delete temp file after done
                upload()->rollUp($this->file->entityId());
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
