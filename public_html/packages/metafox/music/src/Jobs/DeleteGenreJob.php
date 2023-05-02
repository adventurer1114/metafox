<?php

namespace MetaFox\Music\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Music\Models\Genre;
use MetaFox\Music\Repositories\GenreRepositoryInterface;

/**
 * Class DeleteGenreJob.
 * @ignore
 * @codeCoverageIgnore
 */
class DeleteGenreJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Genre $genre;

    protected int $newGenreId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Genre $genre, int $newGenreId)
    {
        $this->genre      = $genre;
        $this->newGenreId = $newGenreId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $genreRepository = resolve(GenreRepositoryInterface::class);
        $genreRepository->deleteOrMoveToNewCategory($this->genre, $this->newGenreId);
    }
}
