<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Only insert if table is empty
         if (User::count() == 0) {
User::create([
'name'=> 'admin',
'username'=> 'admin',
'email'=> 'admin@gmail.com',
'role'=> 1,
'password' => Hash::make('password')

]);

         }
    }
}
