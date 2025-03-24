<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(50)->create();

        $user = User::first();
        $user->name = 'fengniancong';
        $user->email = 'fengniancong@163.com';
        $user->password = bcrypt('123456');
        $user->is_admin = true;
        $user->save();

        $user = User::find(2);
        $user->name = 'honglang';
        $user->email = 'honglang@163.com';
        $user->password = bcrypt('123456');
        $user->save();
    }
}
