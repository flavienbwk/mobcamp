<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Avatar;

class UserTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $avatar = new LasseRafn\InitialAvatarGenerator\InitialAvatar();
        $faker = Faker\Factory::create();
        $users = [];

        // Generating demo user
        $options = [
            'ids' => sha1(uniqid(rand(), true)),
            'username' => "username",
            'password' => bcrypt("password"),
            'email' => $faker->email,
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName
        ];
        try {
            $User = User::firstOrCreate($options);
        } catch (Exception $ex) {
            $options = User::where("username", "username")->first()->toArray();
        }
        if (isset($options["id"]))
            $users[] = $options;


        // Generating 5 users
        for ($i = 0; $i < 5; $i++) {
            $faker = Faker\Factory::create();
            $options = [
                'ids' => sha1(uniqid(rand(), true)),
                'username' => $faker->userName,
                'password' => bcrypt($faker->password()),
                'email' => $faker->email,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName
            ];
            try {
                $User = User::create($options);
                $options["id"] = $User->id;
                $users[] = $options;
                echo "Generated user " . $options["username"] . "\n";
            } catch (Exception $ex) {
                echo "Failed to generate user " . $options["username"] . " : " . $ex->getMessage() . "\n";
            }
        }

        // Generate avatars
        foreach ($users as $user) {
            $file_name = "demo_pics/avatar_" . $user["ids"] . ".png";
            $image = $avatar->name(uniqid() . " " . uniqid())->generate()->save(public_path() . "/" . $file_name);
            if ($image) {
                Avatar::create([
                    "local_uri" => $file_name,
                    "User_id" => $user["id"]
                ]);
            }
        }
    }

}
