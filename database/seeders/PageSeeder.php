<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages = Page::factory(10)->create();

        $pages->find(1)->update(['is_default_home' => true]);
        $pages->find(2)->update(['is_default_not_found' => true]);
    }
}
