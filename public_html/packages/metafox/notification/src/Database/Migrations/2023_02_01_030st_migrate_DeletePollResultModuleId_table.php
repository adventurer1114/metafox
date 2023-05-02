<?php

use Illuminate\Database\Migrations\Migration;
use MetaFox\Notification\Contracts\TypeManager;

/*
 * stub: /packages/database/migration.stub
 */

/*
 * @ignore
 * @codeCoverageIgnore
 * @link \$PACKAGE_NAMESPACE$\Models
 */

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        resolve(TypeManager::class)->handleDeletedModuleId([
            'poll_result',
        ]);

        // to do here
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
