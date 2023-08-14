<?php

namespace Database\Seeders;

use App\Models\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = UserModel::create([
            'user_roles_id' => 1,
            'name' => 'Ari Gunawan Jatmiko',
            'email' => 'arigunawanjatmiko@gmail.com',
            'phone_number' => '085785196574',
            'password' => Hash::make('12345678'),
            'updated_security' => date('Y-m-d H:i:s')
        ]);
    }
}

