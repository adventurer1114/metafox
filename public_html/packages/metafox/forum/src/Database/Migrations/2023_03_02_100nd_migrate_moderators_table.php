<?php

use MetaFox\Platform\Support\DbTableHelper;
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
        if (!Schema::hasTable('forum_user_role_permissions')) {
            Schema::create('forum_user_role_permissions', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('forum_id', false, true)
                    ->index('role_perm_forum_id');
                $table->bigInteger('role_id', false, true)
                    ->index('role_perm_role_id');
                $table->string('permission_name', 150)
                    ->index('role_perm_name');
                $table->boolean('permission_value')
                    ->default(false);
            });
        }

        if (!Schema::hasTable('forum_moderator')) {
            Schema::create('forum_moderator', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('forum_id', false, true)
                    ->index('forum_mor_forum_id');
                DbTableHelper::morphUserColumn($table);
            });
        }

        if (!Schema::hasTable('forum_moderator_access')) {
            Schema::create('forum_moderator_access', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('moderator_id', false, true)
                    ->index('mor_access_moderator_id');
                $table->string('permission_name', 150);
            });
        }

        // to do here
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_user_role_permissions');
        Schema::dropIfExists('forum_moderator');
        Schema::dropIfExists('forum_moderator_access');
    }
};
