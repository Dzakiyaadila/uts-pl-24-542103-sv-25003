<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PostgreSqlConnectionTest extends TestCase
{
    public function test_it_connects_to_the_expected_postgresql_test_database(): void
    {
        $this->assertTrue($this->app->environment('testing'));

        $connection = DB::connection();

        $this->assertSame('pgsql', $connection->getDriverName());
        $this->assertStringEndsWith('_test', $connection->getConfig('database'));

        $identity = $connection->selectOne(
            'select current_database() as database_name, current_user as database_user'
        );

        $this->assertSame(
            $connection->getConfig('database'),
            $identity->database_name
        );

        $this->assertSame(
            $connection->getConfig('username'),
            $identity->database_user
        );

        $this->assertTrue(Schema::hasTable('users'));
    }
}