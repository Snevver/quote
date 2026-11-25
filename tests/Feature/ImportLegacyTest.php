<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ImportLegacyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.connections.legacy' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => false]]);
        Artisan::call('migrate', ['--database' => 'legacy', '--force' => true]);

        $legacy = DB::connection('legacy');
        $legacy->table('users')->insert(['id' => 7, 'name' => 'Snevver', 'email' => 'a@b.c', 'password' => 'hash', 'created_at' => now(), 'updated_at' => now()]);
        $legacy->table('settings')->insert([
            ['key' => 'inchecken_ip_whitelist', 'value' => '1.2.3.4', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'mail_password', 'value' => 'encrypted-with-old-key', 'created_at' => now(), 'updated_at' => now()],
        ]);
        // Folder 1 lives inside folder 2 (parent has the higher id).
        $legacy->table('quote_folders')->insert([
            ['id' => 1, 'name' => 'Stoic', 'parent_id' => 2, 'position' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Philosophy', 'parent_id' => null, 'position' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
        $legacy->table('quotes')->insert(['id' => 5, 'quote' => 'Memento mori', 'author' => 'Marcus', 'tags_en' => '["life"]', 'folder_id' => 1, 'position' => 0, 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_it_copies_and_verifies_without_touching_legacy(): void
    {
        $this->artisan('app:import-legacy')->assertSuccessful();

        $this->assertSame(['inchecken_ip_whitelist'], DB::table('settings')->pluck('key')->all());
        $this->assertSame(2, DB::table('quote_folders')->where('id', 1)->value('parent_id'));
        $this->assertSame(1, DB::table('quotes')->where('id', 5)->value('folder_id'));
        $this->assertSame(1, DB::connection('legacy')->table('quotes')->count());
    }

    public function test_it_refuses_to_import_into_a_non_empty_database(): void
    {
        DB::table('users')->insert(['name' => 'x', 'email' => 'x@y.z', 'password' => 'x']);

        $this->artisan('app:import-legacy')->assertFailed();
        $this->assertSame(0, DB::table('quotes')->count());
    }
}
