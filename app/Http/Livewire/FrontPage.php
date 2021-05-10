<?php

namespace App\Http\Livewire;

use App\Models\Page;
use Livewire\Component;

class FrontPage extends Component
{
    public Page $page;

    /**
     * livewire mount method
     *
     * @param Page $page
     * @return void
     */
    public function mount(Page $page): void
    {
        $this->page = $page;
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
