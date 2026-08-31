<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('name')->nullable()->after('user_id');
            $table->string('access_token', 64)->nullable()->unique()->after('idempotency_key');
        });

        $this->setUserIdNullable('orders');
    }

    public function down(): void
    {
        DB::table('orders')->whereNull('user_id')->delete();

        if (DB::getDriverName() !== 'sqlite') {
            $foreign = 'orders_user_id_foreign';
            DB::statement("ALTER TABLE orders DROP FOREIGN KEY {$foreign}");
            DB::statement('ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NOT NULL');
            DB::statement("ALTER TABLE orders ADD CONSTRAINT {$foreign} FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['name', 'access_token']);
        });
    }

    /**
     * Drop the NOT NULL constraint on {$table}.user_id. MySQL supports this
     * in place; SQLite (used in tests) has no ALTER COLUMN, so it needs the
     * classic rename/recreate/copy dance.
     */
    private function setUserIdNullable(string $table): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->sqliteDropNotNull($table, 'user_id');

            return;
        }

        $foreign = "{$table}_user_id_foreign";
        DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$foreign}");
        DB::statement("ALTER TABLE {$table} MODIFY user_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$foreign} FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
    }

    private function sqliteDropNotNull(string $table, string $column): void
    {
        $sql = DB::selectOne("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ?", [$table])->sql;

        $quoted = preg_quote('"'.$column.'"', '/');
        $patched = preg_replace('/('.$quoted.'\s+\w+)\s+not null/i', '$1', $sql, 1);

        DB::statement('PRAGMA foreign_keys=OFF');
        // Without this, SQLite's rename silently rewrites the FK clause in
        // *other* tables (e.g. order_items.order_id) to point at "{table}_old",
        // which then dangles once that table is dropped below.
        DB::statement('PRAGMA legacy_alter_table=ON');
        DB::statement("ALTER TABLE {$table} RENAME TO {$table}_old");
        DB::statement($patched);

        $columns = collect(DB::select("PRAGMA table_info({$table})"))
            ->pluck('name')
            ->map(fn ($name) => '"'.$name.'"')
            ->implode(',');
        DB::statement("INSERT INTO {$table} ({$columns}) SELECT {$columns} FROM {$table}_old");
        DB::statement("DROP TABLE {$table}_old");
        DB::statement('PRAGMA legacy_alter_table=OFF');
        DB::statement('PRAGMA foreign_keys=ON');
    }
};
