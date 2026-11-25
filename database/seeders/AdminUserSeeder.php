<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('services.admin.email', 'admin@snev.dev')],
            [
                'name' => 'Snevver',
                'password' => Hash::make('Password'),
            ]
        );
    }
}
