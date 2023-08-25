<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterRawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = base_path() . '../resources/sql/MasterRawSeeder.sql';
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
