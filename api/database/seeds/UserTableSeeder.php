<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Avatar;
use App\Cooperative;
use App\CooperativeUser;
use App\CooperativeUserRole;

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

        // Removing previous users
        CooperativeUserRole::orderBy("created_at")->delete();
        CooperativeUser::orderBy("created_at")->delete();
        Avatar::orderBy("user_id")->delete();
        User::orderBy("username")->delete();
        $files = glob(public_path() . '/demo_pics/*.png'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }

        // Getting cooperatives
        $cooperatives = Cooperative::all()->toArray();

        // Generating demo user
        $options = [
            'ids' => sha1(uniqid(rand(), true)),
            'username' => "username@domain.com",
            'password' => bcrypt("password"),
            'email' => "username@domain.com",
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName
        ];
        try {
            $User = User::firstOrCreate($options);
            CooperativeUser::create([
                "user_id" => $User->id,
                "cooperative_id" => $cooperatives[rand(0, sizeof($cooperatives) - 1)]["id"]
            ]);
        } catch (Exception $ex) {
            $options = User::where("username", "username")->first()->toArray();
        }
        if (isset($options["id"]))
            $users[] = $options;

        // Generating 5 users
        for ($i = 0; $i < 5; $i++) {
            $faker = Faker\Factory::create();
            $email = $faker->email;
            $password = $faker->password();
            $options = [
                'ids' => sha1(uniqid(rand(), true)),
                'username' => $email,
                'password' => bcrypt($password),
                'email' => $email,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName
            ];
            try {
                $User = User::create($options);
                CooperativeUser::create([
                    "user_id" => $User->id,
                    "cooperative_id" => $cooperatives[rand(0, sizeof($cooperatives) - 1)]["id"]
                ]);

                $options["id"] = $User->id;
                $users[] = $options;
                echo "Generated user > " . $options["username"] . " with password > " . $password . "\n";
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
