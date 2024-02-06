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
        User::factory(20)->create();
        User::factory()->create([
            'name'=>'Bang User',
            'email'=>'user@gmail.com',
            'email_verified_at'=> now(),
            'role'=> 'user',
            'password'=> Hash::make('user123'),

        ]);
        User::factory()->create([
            'name'=>'Bang Admin',
            'email'=>'admin@gmail.com',
            'email_verified_at'=> now(),
            'role'=> 'admin',
            'phone'=> '628985454555',
            'bio' => 'Flutter Developer',
            'password'=> Hash::make('admin123'),

        ]);
        User::factory()->create([
            'name'=>'Bang Super Admin',
            'email'=>'superadmin@gmail.com',
            'email_verified_at'=> now(),
            'role'=> 'superadmin',
            'phone'=> '628985454555',
            'bio' => 'Laravel Developer',
            'password'=> Hash::make('superadmin123'),

        ]);
    }
}
