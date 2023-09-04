<?php

namespace Database\Seeders;

use App\Models\ProductCategoryModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        ProductCategoryModel::truncate();
        Schema::enableForeignKeyConstraints();

        $role = [
            ['id' => 1, 'name' => 'Makanan'],
            ['id' => 2, 'name' => 'Minuman'],
        ];

        foreach ($role as $nilai) {
            ProductCategoryModel::create([
                'id' => $nilai['id'],
                'name' => $nilai['name'],
            ]);
        }
    }
}
