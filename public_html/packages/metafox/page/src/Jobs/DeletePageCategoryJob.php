<?php

namespace MetaFox\Page\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Page\Models\Category;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;

/**
 * Class DeletePageCategoryJob.
 */
class DeletePageCategoryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Category $category;

    protected int $newCategoryId;
    protected int $newTypeId;

    /**
     * DeleteCategoryJob constructor.
     *
     * @param Category $category
     * @param int      $newCategoryId
     * @param int      $newTypeId
     */
    public function __construct(Category $category, int $newCategoryId, int $newTypeId)
    {
        $this->category = $category;
        $this->newCategoryId = $newCategoryId;
        $this->newTypeId = $newTypeId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $categoryRepository = resolve(PageCategoryRepositoryInterface::class);
        $categoryRepository->deleteOrMoveToNewCategory($this->category, $this->newCategoryId);
    }
}
