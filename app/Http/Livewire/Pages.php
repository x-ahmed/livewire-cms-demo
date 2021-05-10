<?php

namespace App\Http\Livewire;

use Debugbar;
use App\Models\Page;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Livewire\WithPagination;

class Pages extends Component
{
    use WithPagination;

    /**
     * component state variables.
     *
     * @var array
     */
    public array $state = [
        'data' => [
            'title'    => null,
            'slug'     => null,
            'body'     => null,
            'id'       => null,
        ],
        'ui'   => [
            'isModalUp' => false,
        ],
    ];

    /**
     * livewire mount function.
     *
     * @return void
     */
    public function mount(): void
    {
        // reset page after reloading.
        $this->resetPage();
    }

    /**
     * reset component state on cancel.
     *
     * @return void
     */
    public function cancel(): void
    {
        $this->resetErrorBag();
        $this->resetState();
    }

    /**
     * generate slug from the given string value.
     *
     * @param string $value
     * @return void
     */
    private function generateSlug(string $value): void
    {
        $this->state['data']['slug'] = Str::slug($value);
    }

    /**
     * update slug on title changes.
     *
     * @param  string $title
     * @return void
     */
    public function updatedStateDataTitle(string $title): void
    {
        $this->generateSlug($title);
    }

    /**
     * reset component to its default state.
     *
     * @return void
     */
    private function resetState(): void
    {
        \array_walk_recursive(
            array: $this->state,
            callback: fn (&$val, $key, string $arg) => $val = $key === $arg ? false : null,
            arg: 'isModalUp'
        );
    }

    /**
     * overwrite component's state with the given data.
     *
     * @param array $currentState
     * @return void
     */
    private function overwriteState(array $currentState): void
    {
        \array_walk(
            array: $this->state,
            callback: fn (&$val, $key, array $arg) => $val = $arg[$key],
            arg: $currentState
        );
    }

    /**
     * reset component state and show success alert.
     *
     * @return void
     */
    private function handleUIEffects(): void
    {
        $this->resetState();
        $this->alert(
            'success',
            'Submitted successfully!',
            config('livewire-alert.success')
        );
    }

    /**
     * form validation rules.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'state.data.title' => 'required',
            'state.data.slug'  => [
                'required',
                Rule::unique('pages', 'slug')->ignore($this->state['data']['id']),
            ],
            'state.data.body'  => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * form validation attribute names.
     *
     * @return array
     */
    protected function validationAttributes(): array
    {
        return [
            'state.data.title' => 'title',
            'state.data.slug'  => 'slug',
            'state.data.body'  => 'body',
        ];
    }

    /**
     * update model page instance.
     *
     * @param array $data
     * @param array $currentState
     * @return void
     */
    private function update(array $data, array $currentState): void
    {
        try {
            $page = Page::findOrFail($this->state['data']['id']);
            $page->update($data);
        } catch (\Throwable $e) {
            $this->overwriteState($currentState);
            $this->alert(
                'error',
                'Oops, Something went wrong',
                config('livewire-alert.alert')
            );
        }
    }

    /**
     * store new page instance.
     *
     * @param array $data
     * @param array $currentState
     * @return void
     */
    private function store(array $data, array $currentState): void
    {
        try {
            Page::create($data);
        } catch (\Throwable $e) {
            $this->overwriteState($currentState);
            $this->alert(
                'error',
                'Oops, Something went wrong',
                config('livewire-alert.alert')
            );
        }
    }

    /**
     * get valid data.
     *
     * @return array
     */
    private function getValidData(): array
    {
        $valid = $this->validate();
        return $valid['state']['data'];
    }

    /**
     * handle UI effects and model page instance.
     *
     * @return void
     */
    public function save(): void
    {
        $data = $this->getValidData();
        $currentState = $this->state;

        (!$this->state['data']['id'])
            ? $this->store($data, $currentState)
            : $this->update($data, $currentState);

        $this->handleUIEffects();
    }

    /**
     * parse model instance's data into state.
     *
     * @param int $id
     * @return void
     */
    private function parseModelDataIntoState(int $id): void
    {
        $page = Page::findOrFail($id);
        $this->state['data']['title']    = $page->title;
        $this->state['data']['slug']     = $page->slug;
        $this->state['data']['body']     = $page->body;
        $this->state['data']['id']       = $page->id;
    }

    /**
     * pop the form modal up.
     *
     * @return void
     */
    private function showModal(): void
    {
        $this->state['ui']['isModalUp'] = true;
    }


    /**
     * Listen to emitted events
     *
     * @var array
     */
    protected $listeners = [
        'confirmed',
        'cancelled',
    ];

    /**
     * Show Delete Modal.
     *
     * @param integer $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->state['data']['id'] = $id;
        $this->confirm(
            'Are you sure deleting this post? 👋',
            [
                'toast'             => false,
                'position'          => 'center',
                'showConfirmButton' => true,
                'cancelButtonText'  => 'No',
                'onConfirmed'       => 'confirmed',
                'onCancelled'       => 'cancelled',
            ]
        );
    }

    /**
     * confirm delete model instance listener.
     *
     * @return void
     */
    public function confirmed()
    {
        Page::findOrFail($this->state['data']['id'])->delete();

        $this->resetPage(); // back to pagination first page

        $this->alert(
            'success',
            'Post deleted successfully 👍'
        );
    }

    /**
     * cancel delete model instance listener.
     *
     * @return void
     */
    public function cancelled()
    {
        $this->alert('info', 'Understood');
    }

    /**
     * handle edit form model
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id)
    {
        $this->resetState();
        $this->resetErrorBag();
        $this->showModal();
        $this->parseModelDataIntoState($id);
    }

    /**
     * pop the form modal up.
     *
     * @return void
     */
    public function create(): void
    {
        $this->resetState();
        $this->resetErrorBag();
        $this->showModal();
    }

    /**
     * render component presentational UI.
     *
     * @return View|Factory
     */
    public function render(): View|Factory
    {
        return view('livewire.pages', [
            'pages' => Page::paginate(),
        ]);
    }
}
