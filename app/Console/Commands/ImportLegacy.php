<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One-time copy of this app's tables from the original snev.dev database
 * (the `legacy` connection) into this app's own database.
 *
 * The legacy database is only ever read (SELECT). Run it against an empty,
 * freshly migrated database; it refuses to run when a target table has rows.
 * Every table is verified afterwards (row count + content hash).
 */
class ImportLegacy extends Command
{
    protected $signature = 'app:import-legacy {--dry-run : Only show what would be copied}';

    protected $description = 'Copy this app\'s data from the original snev.dev database (read-only) and verify it';

    /**
     * Tables in parent-before-child order. A callable filters the legacy rows.
     */
    protected function tables(): array
    {
        return [
            'users' => null,
            'settings' => fn ($q) => $q->where('key', 'inchecken_ip_whitelist'),
            'quote_folders' => null,
            'quotes' => null,
        ];
    }

    public function handle(): int
    {
        $legacy = DB::connection('legacy');
        $tables = $this->tables();

        $this->info('Source: '.$legacy->getDatabaseName().' → target: '.DB::connection()->getDatabaseName());

        foreach ($tables as $table => $filter) {
            if (! Schema::hasTable($table)) {
                $this->error("Target table `{$table}` does not exist. Run `php artisan migrate` first.");

                return self::FAILURE;
            }
            if (DB::table($table)->exists()) {
                $this->error("Target table `{$table}` is not empty. This command only imports into an empty database.");

                return self::FAILURE;
            }
            if (! $legacy->getSchemaBuilder()->hasTable($table)) {
                $this->error("Legacy table `{$table}` does not exist.");

                return self::FAILURE;
            }
        }

        if ($this->option('dry-run')) {
            $this->table(['table', 'rows to copy'], collect($tables)->map(
                fn ($filter, $table) => [$table, $this->source($table, $filter)->count()]
            )->values()->all());

            return self::SUCCESS;
        }

        // Rows keep their ids; self-references (e.g. a folder whose parent has a
        // higher id) are only consistent once every row is in, so FK checks are
        // paused for the copy and the result is verified below.
        Schema::disableForeignKeyConstraints();
        DB::transaction(function () use ($tables) {
            foreach ($tables as $table => $filter) {
                $columns = $this->columns($table);
                $this->source($table, $filter)
                    ->select($columns)
                    ->orderBy($columns[0])
                    ->chunk(500, function ($rows) use ($table) {
                        DB::table($table)->insert($rows->map(fn ($row) => (array) $row)->all());
                    });
            }
        });
        Schema::enableForeignKeyConstraints();

        $rows = [];
        $ok = true;
        foreach ($tables as $table => $filter) {
            $source = $this->fingerprint($this->source($table, $filter), $table);
            $target = $this->fingerprint(DB::table($table), $table);
            $match = $source === $target;
            $ok = $ok && $match;
            $rows[] = [$table, $source['count'], $target['count'], $match ? 'OK' : 'MISMATCH'];
        }

        $this->table(['table', 'legacy rows', 'copied rows', 'check'], $rows);

        if (! $ok) {
            $this->error('Verification failed. The legacy database was not changed; wipe this database (migrate:fresh) and investigate before retrying.');

            return self::FAILURE;
        }

        $this->info('All tables copied and verified. The legacy database was only read.');

        return self::SUCCESS;
    }

    protected function source(string $table, ?callable $filter): Builder
    {
        $query = DB::connection('legacy')->table($table);

        return $filter ? $filter($query) : $query;
    }

    /** Columns that exist in both the legacy table and this app's table. */
    protected function columns(string $table): array
    {
        $target = Schema::getColumnListing($table);
        $legacy = DB::connection('legacy')->getSchemaBuilder()->getColumnListing($table);

        return array_values(array_intersect($target, $legacy));
    }

    /** Row count plus a hash over every copied value, in primary-key order. */
    protected function fingerprint(Builder $query, string $table): array
    {
        $columns = $this->columns($table);
        $hash = hash_init('sha256');
        $count = 0;

        (clone $query)->select($columns)->orderBy($columns[0])->chunk(500, function ($rows) use ($hash, &$count) {
            foreach ($rows as $row) {
                hash_update($hash, json_encode(array_map(fn ($v) => $v === null ? null : (string) $v, (array) $row)));
                $count++;
            }
        });

        return ['count' => $count, 'hash' => hash_final($hash)];
    }
}
