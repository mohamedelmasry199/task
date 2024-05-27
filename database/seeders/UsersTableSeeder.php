<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'user_name' => $faker->name,
                'mobile_number' => $faker->numerify('##########'),
                'password' => Hash::make('123456789'),
                'role' => 'user',
            ]);
        }
    }
}

