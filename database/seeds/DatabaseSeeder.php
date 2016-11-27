<?php

use Illuminate\Database\Seeder;
use Scool\Curriculum\Database\Seeds\LawsTableSeeder;
use Scool\Curriculum\Database\Seeds\SubmoduleTypesTableSeeder;

/**
 * Class DatabaseSeeder.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(SubmoduleTypesTableSeeder::class);
        $this->call(LawsTableSeeder::class);
    }
}
