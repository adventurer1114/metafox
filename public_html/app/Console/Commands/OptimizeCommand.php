<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OptimizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize platform command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->components->info('Caching the framework bootstrap files.');

        collect([
            'permission:cache-reset' => fn () => $this->callSilent('permission:cache') == 0,
            'event:cache'            => fn () => $this->callSilent('event:cache') == 0,
            'view:cache'             => fn () => $this->callSilent('view:cache') == 0,
            'cache:clear'            => fn () => $this->callSilent('cache:clear') == 0,
            'route:cache'            => fn () => $this->callSilent('route:cache') == 0,
            'config:cache'           => fn () => $this->callSilent('config:cache') == 0,
        ])->each(fn ($task, $description) => $this->components->task($description, $task));

        $this->newLine();

        return Command::SUCCESS;
    }
}
