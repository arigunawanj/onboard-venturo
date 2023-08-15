<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Customer::truncate();
        Schema::enableForeignKeyConstraints();

        $faker = Faker::create('id_ID');

        for ($i=1; $i <= 50 ; $i++) {
            Customer::create([
                'id' => $faker->uuid,
                'name' => $faker->name,
                'email' => $faker->email,
                'phone_number' => $faker->phoneNumber,
                'date_of_birth' => $faker->date,
                'photo' => $faker->imageUrl(640, 480, 'animals', true),
            ]);
        }
    }
}
