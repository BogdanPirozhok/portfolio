<?php

namespace Database\Seeders;

use App\Models\Profession;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            'bogdan_pirozhok',
            'd_maydanyuk',
            'Vysokovich_Kirill'
        ];

        foreach ($users as $user) {
            User::query()->firstOrCreate(
                ['username' => $user]
            );
        }
    }
}
