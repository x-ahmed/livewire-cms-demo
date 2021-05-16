<?php

namespace Database\Seeders;

use App\Models\NavMenu;
use Illuminate\Database\Seeder;

class NavMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NavMenu::factory(10)->create();
    }
}
