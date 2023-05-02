<?php

namespace MetaFox\Forum\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use MetaFox\Forum\Models\Forum;
use MetaFox\Forum\Repositories\ForumRepositoryInterface;

class PackageSeeder extends Seeder
{
    protected $repository;

    /**
     * @param ForumRepositoryInterface $repository
     */
    public function __construct(ForumRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array[]
     */
    public function getDefaultForums(): array
    {
        return [
            [
                'title'      => 'Discussions',
                'ordering'   => 1,
                'level'      => 1,
                'sub_forums' => [
                    [
                        'title'    => 'General',
                        'ordering' => 1,
                        'level'    => 2,
                    ],
                    [
                        'title'    => 'Movies',
                        'ordering' => 2,
                        'level'    => 2,
                    ],
                    [
                        'title'    => 'Music',
                        'ordering' => 3,
                        'level'    => 2,
                    ],
                ],
            ],
            [
                'title'      => 'Computers & Technology',
                'ordering'   => 2,
                'level'      => 1,
                'sub_forums' => [
                    [
                        'title'    => 'Computers',
                        'ordering' => 1,
                        'level'    => 2,
                    ],
                    [
                        'title'    => 'Electronics',
                        'ordering' => 2,
                        'level'    => 2,
                    ],
                    [
                        'title'    => 'Gadgets',
                        'ordering' => 3,
                        'level'    => 2,
                    ],
                    [
                        'title'    => 'General',
                        'ordering' => 4,
                        'level'    => 2,
                    ],
                ],
            ],
        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $isExists = $this->repository->getModel()
            ->newQuery()
            ->exists();

        if ($isExists) {
            $this->upgradeForums();
            return;
        }

        $this->installForums();
    }

    protected function installForums(): void
    {
        foreach ($this->getDefaultForums() as $forum) {
            $model = $this->repository->create([
                'title'    => $forum['title'],
                'ordering' => $forum['ordering'],
            ]);

            if ($model instanceof Forum && $model->entityId()) {
                $subForums = Arr::get($forum, 'sub_forums', []);

                foreach ($subForums as $subForum) {
                    $this->repository->create(array_merge($subForum, [
                        'parent_id' => $model->entityId(),
                    ]));
                }

                if (count($subForums)) {
                    $model->update(['total_sub' => count($subForums)]);
                }
            }
        }
    }

    protected function upgradeForums(): void
    {
        $upgradedForums = Forum::query()
            ->with(['parentForums'])
            ->where('parent_id', '<>', 0)
            ->where('level', '=', 1)
            ->get();

        if (!$upgradedForums->count()) {
            return;
        }

        foreach ($upgradedForums as $upgradedForum) {
            if (null === $upgradedForum->parentForums) {
                $upgradedForum->update(['parent_id' => 0]);
                continue;
            }

            $upgradedForum->update(['level' => (int) $upgradedForum->parentForums->level + 1]);
        }
    }
}
