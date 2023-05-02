<?php

namespace MetaFox\Photo\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Photo\Models\Category;
use MetaFox\Photo\Repositories\CategoryRepositoryInterface;

/**
 * Class DeleteCategoryJob.
 */
class DeleteCategoryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var Category */
    protected Category $category;

    protected int $newCategoryId;

    /**
     * DeleteCategoryJob constructor.
     *
     * @param Category $category
     * @param int      $newCategoryId
     */
    public function __construct(Category $category, int $newCategoryId)
    {
        $this->category = $category;
        $this->newCategoryId = $newCategoryId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $categoryRepository = resolve(CategoryRepositoryInterface::class);
        $categoryRepository->deleteOrMoveToNewCategory($this->category, $this->newCategoryId);
    }
}
