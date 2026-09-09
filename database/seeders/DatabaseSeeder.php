<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'customer@karnacab.local'],
            [
                'name' => 'Fazlu',
                'password' => 'ChangeMe@123',
                'role' => 'CUSTOMER',
            ],
        );
    }
}
