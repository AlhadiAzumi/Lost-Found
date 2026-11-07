<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indexes for the hottest read paths (catalog, home stats, admin lists).
     *
     * @var array<int, array{0: string, 1: array<int, string>, 2: string}>
     */
    private array $indexes = [
        ['items', ['campus_id', 'status', 'date_event'], 'items_campus_status_date_event_idx'],
        ['items', ['campus_id', 'type'], 'items_campus_type_idx'],
        ['items', ['campus_id', 'location'], 'items_campus_location_idx'],
        ['items', ['campus_id', 'id'], 'items_campus_id_idx'],
        ['items', ['user_id'], 'items_user_id_idx'],
        ['claims', ['campus_id', 'status'], 'claims_campus_status_idx'],
        ['claims', ['item_id', 'status'], 'claims_item_status_idx'],
        ['claims', ['campus_id', 'id'], 'claims_campus_id_idx'],
        ['claims', ['user_id'], 'claims_user_id_idx'],
        ['handovers', ['item_id'], 'handovers_item_id_idx'],
    ];

    public function up(): void
    {
        foreach ($this->indexes as [$table, $columns, $name]) {
            if (! Schema::hasTable($table) || $this->hasIndex($table, $name)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($columns, $name): void {
                $blueprint->index($columns, $name);
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->indexes) as [$table, , $name]) {
            if (! Schema::hasTable($table) || ! $this->hasIndex($table, $name)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($name): void {
                $blueprint->dropIndex($name);
            });
        }
    }

    private function hasIndex(string $table, string $name): bool
    {
        return collect(Schema::getIndexes($table))->contains(
            fn (array $index): bool => ($index['name'] ?? null) === $name
        );
    }
};
