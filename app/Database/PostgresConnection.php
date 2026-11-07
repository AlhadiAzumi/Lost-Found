<?php

namespace App\Database;

use Illuminate\Database\PostgresConnection as BasePostgresConnection;

class PostgresConnection extends BasePostgresConnection
{
    /**
     * Prepare the query bindings for execution.
     *
     * PDO's emulated prepares are enabled for Supabase's transaction pooler.
     * In that mode PDO inlines booleans as integer literals (1/0), which
     * PostgreSQL rejects for boolean columns ("operator does not exist:
     * boolean = integer"). Binding them as "1"/"0" strings lets PostgreSQL
     * coerce them to boolean with either native or emulated prepares.
     */
    public function prepareBindings(array $bindings)
    {
        foreach ($bindings as $key => $value) {
            if (is_bool($value)) {
                $bindings[$key] = $value ? '1' : '0';
            }
        }

        return parent::prepareBindings($bindings);
    }
}
