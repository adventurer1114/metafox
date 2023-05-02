<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use MetaFox\User\Models\User;
use MetaFox\User\Support\Commands\UserGeneratorTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * @SuppressWarnings(PHPMD)
 */
class GenerateDataCommand extends Command
{
    use UserGeneratorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'data:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sample data.';

    private bool $testMode = false;

    private ?string $username;

    private int $sampleNeed = 1;

    private ?User $testUser = null;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->testMode   = $this->option('test');
        $content          = $this->argument('content');
        $this->sampleNeed = (int) $this->option('count');
        $this->sampleNeed = $this->sampleNeed > 1 ? $this->sampleNeed : 1;

        if (!$this->initTestUser()) {
            return 1;
        }

        if ($content) {
            $modelClass = Relation::getMorphedModel($content);

            if (!$modelClass || !class_exists($modelClass)) {
                $this->error('Could not found realtion of ' . $content);

                return static::FAILURE;
            }

            $this->info(sprintf('Creating %d %s', number_format($this->sampleNeed), $content));

            $this->generateData($modelClass, $this->sampleNeed);

            return 0;
        }

        do {
            $okay = $this->process();
        } while ($okay);

        return 0;
    }

    public function initTestUser(): bool
    {
        if (!$this->option('user')) {
            return true;
        }

        $username       = $this->option('user');
        $this->testUser = User::query()->where('id', (int) $username)
            ->orWhere('user_name', $username)
            ->orWhere('email', $username)
            ->first();

        if (!$this->testUser) {
            $this->error('Invalid user ' . $username);

            return false;
        }

        return true;
    }

    public function process(): int
    {
        $file = storage_path('framework/sample-data.php');
        if (!file_exists($file)) {
            return 0;
        }

        $config = require $file;

        @ini_set('memory_limit', '-1');

        $generators = [];
        foreach ($config as $item) {
            $content = $item['content'];
            $limit   = $item['limit'] ?? 1000;
            $chunk   = $item['chunk'] ?? 5;

            if (!$limit) {
                continue;
            }

            $modelClass = Relation::getMorphedModel($content);

            if (!$modelClass || !class_exists($modelClass)) {
                $this->error('Unexpected morphed model for ' . $content);

                continue;
            }

            $need = $this->testMode ? $this->sampleNeed : $this->needItem($modelClass, $limit, $chunk);

            if ($need > 0) {
                $taskName = sprintf('Creating %s %s', $need, $content);

                $generators[$taskName] = fn () => $this->generateData($modelClass, $need) == $need;
            }
        }

        collect($generators)->each(fn ($task, $description) => $this->components->task($description, $task));

        if ($this->testMode || !count($generators)) {
            return 0;
        }

        return 1;
    }

    public function pickAuthUser()
    {
        $user = $this->testUser ? $this->testUser : User::all()->random();
        Auth::setUser($user);

        return $user;
    }

    public function needItem($modelClass, $limit, $chunk = 100): int
    {
        $existing = $modelClass::count();
        $needItem = $limit - $existing;
        $needItem = $needItem > $chunk ? $chunk : $needItem;

        return $needItem;
    }

    public function getArguments()
    {
        return [
            ['content', InputArgument::OPTIONAL],
        ];
    }

    public function generateData(string $modelClass, int $need): int
    {
        $user = $this->pickAuthUser();

        /** @var Model $modelInstance */
        $modelInstance = resolve($modelClass);

        /** @var Factory $factory */
        $factory = $modelInstance::factory();

        $factory = $factory->count($need);

        if (method_exists($factory, 'setUserAndOwner')) {
            $factory = $factory->setUserAndOwner($user, $user);
        }
        if (method_exists($factory, 'seed')) {
            $factory = $factory->seed();
        }

        $factory->create([]);

        return $need;
    }

    public function getOptions()
    {
        return [
            ['test', 't', InputOption::VALUE_NONE, 'Run tests?'],
            ['user', 'u', InputOption::VALUE_OPTIONAL, 'User of content, etc: admin?'],
            ['count', 'c', InputOption::VALUE_OPTIONAL, 'User of content, etc: admin?'],
        ];
    }
}
