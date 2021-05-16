<?php

namespace App\Http\Livewire;

use App\Models\Page;
use App\Models\NavMenu;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class FrontPage extends Component
{
    public $sidebarLinks;
    public $navbarLinks;

    /**
     * component state variables.
     *
     * @var array
     */
    public array $state = [
        'data' => [
            'title'   => null,
            'slug'    => null,
            'content' => null,
            'id'      => null,
        ],
        'ui'   => [
            'isModalUp' => false,
        ],
    ];

    /**
     * map mounted props to component state
     *
     * @param Page $page
     * @return void
     */
    public function mapPropsToState(Page $page): void
    {
        // $this->page = $page;
        $page->id ?? $page = Page::whereIsDefaultHome(true)->first();
        $this->state['data']['title']   = $page->title;
        $this->state['data']['slug']    = $page->slug;
        $this->state['data']['content'] = $page->body;
        $this->state['data']['id']      = $page->id;
    }

    /**
     * livewire mount method
     *
     * @param Page $page
     * @return void
     */
    public function mount(Page $page): void
    {
        $this->mapPropsToState($page);
        $this->sidebarLinks();
        $this->navbarLinks();
    }

    /**
     * get sidebar links.
     *
     * @return void
     */
    private function sidebarLinks(): void
    {
        $this->sidebarLinks = DB::table('nav_menus')
            ->whereType('side')
            ->orderBy('sequence')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * get navbar links.
     *
     * @return void
     */
    private function navbarLinks(): void
    {
        $this->navbarLinks = DB::table('nav_menus')
            ->whereType('top')
            ->orderBy('sequence')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * livewire render method
     *
     * @return void
     */
    public function render()
    {
        return view('livewire.front-page')->layout('layouts.front-page');
    }
}
