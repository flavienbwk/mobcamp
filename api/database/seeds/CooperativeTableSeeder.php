<?php

use Illuminate\Database\Seeder;
use App\Cooperative;

class CooperativeTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $faker = Faker\Factory::create();

        try {
            Cooperative::orderBy('name')->delete();

            for ($i = 0; $i < 5; $i++) {
                try {
                    Cooperative::firstOrCreate([
                        "name" => "Coop " . $faker->name,
                        "geolocation" => $faker->latitude() . "," . $faker->longitude(),
                        "lang" => "fra"
                    ]);
                } catch (Exception $ex) {
                    echo $ex->getMessage() . "<>\n";
                }
            }
        } catch (Exception $ex) {
            echo $ex->getMessage() . "<\n";
        }
    }

}
