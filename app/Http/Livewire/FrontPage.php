<?php

namespace App\Http\Livewire;

use App\Models\Page;
use Livewire\Component;

class FrontPage extends Component
{
    // public Page $page;

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
