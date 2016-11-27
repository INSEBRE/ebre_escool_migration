<?php

use Illuminate\Database\Seeder;

/**
 * Class UsersTableSeeder.
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Run the user table seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = App\User::firstOrNew([
            'email' => 'sergiturbadenas@gmail.com'
        ]);
        $user->name = "Sergi Tur Badenas";
        $user->password = bcrypt(env('ADMIN_USER_PASSWORD','secret'));
        $user->remember_token = str_random(10);
        $user->save();
    }
}