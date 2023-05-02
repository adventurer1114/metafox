<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MetaFox\Core\Jobs\UpdateAdminSearch;

class UpdateAdminSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metafox:update-admin-search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update admin search entries';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        UpdateAdminSearch::dispatchSync();

        return 0;
    }
}
