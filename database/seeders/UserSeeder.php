<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        User::create([
            'role_id' => 1,
            'first_name' => 'Sagar',
            'last_name' => 'Rathod',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        
    }
}