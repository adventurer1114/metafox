<?php

namespace MetaFox\Like\Database\Seeders;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use MetaFox\Like\Repositories\ReactionRepositoryInterface;
use MetaFox\Platform\PackageManager;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class PackageSeeder.
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSeeder extends Seeder
{
    /**
     * @var ReactionRepositoryInterface
     */
    private ReactionRepositoryInterface $reactionRepository;

    /**
     * LikeDatabaseSeeder constructor.
     *
     * @param ReactionRepositoryInterface $reactionRepository
     */
    public function __construct(ReactionRepositoryInterface $reactionRepository)
    {
        $this->reactionRepository = $reactionRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ValidatorException|FileNotFoundException
     */
    public function run()
    {
        $this->importReactions();
    }

    /**
     * @throws ValidatorException|FileNotFoundException
     */
    public function importReactions()
    {
        if ($this->reactionRepository->getModel()->newQuery()->exists()) {
            return;
        }

        $config = PackageManager::getConfig('metafox/like');

        $reactions = $config['reactions'];

        foreach ($reactions as $reaction) {
            $this->reactionRepository->create($reaction);
        }
    }
}
