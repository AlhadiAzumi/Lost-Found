<?php

namespace Tests\Unit;

use App\Database\PostgresConnection;
use PDO;
use PHPUnit\Framework\TestCase;

class PostgresConnectionTest extends TestCase
{
    public function test_boolean_bindings_are_sent_as_strings(): void
    {
        $connection = new PostgresConnection(new PDO('sqlite::memory:'), 'database');

        $this->assertSame(
            ['1', '0', 'text', 7],
            $connection->prepareBindings([true, false, 'text', 7])
        );
    }
}
