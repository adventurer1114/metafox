<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        if (!Schema::hasColumn('event_categories', 'level')) {
            Schema::table('event_categories', function (Blueprint $table) {
                $table->unsignedInteger('level')
                    ->default(1);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn('event_categories', 'level')) {
            Schema::table('event_categories', function (Blueprint $table) {
                $table->dropColumn(['level']);
            });
        }
    }
};
