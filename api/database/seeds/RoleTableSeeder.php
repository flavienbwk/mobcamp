<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $roles = [
            "enseignant",
            "correcteur",
            "vendeur",
            "acheteur",
            "contrôleur",
            "commercial",
            "administrateur"
        ];
        
        foreach ($roles as $role) {
            try {
                Role::firstOrCreate([
                    "name" => $role
                ]);
            } catch (Exception $ex) {
                echo $ex->getMessage() . "\n";
            }
        }
    }
    
}