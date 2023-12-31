<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();
        UserRole::truncate();
        Schema::enableForeignKeyConstraints();

        $role = [
            ['id' => 1, 'name' => 'Admin', 'access' => 'Admin'],
            ['id' => 2, 'name' => 'Kitchen', 'access' => 'Kitchen'],
        ];

        foreach ($role as $nilai) {
            UserRole::insert([
                'id' => $nilai['id'],
                'name' => $nilai['name'],
                'access' => $nilai['access'],
            ]);
        }


    }
}
