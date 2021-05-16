<?php

namespace App\Http\Livewire;

use App\Models\NavMenu;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;

class NavMenus extends Component
{
    /**
     * livewire pagination trait.
     */
    use WithPagination;

    public array $state = [
        'data' => [
            'id'       => null,
            'sequence' => 1,
            'type'     => 'side',
            'label'    => null,
            'slug'     => null,
        ],
        'ui'   => [
            'isModalUp' => false,
        ],
    ];

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
     * update slug on label changes.
     *
     * @param  string $label
     * @return void
     */
    public function updatedStateDataLabel(string $label): void
    {
        $this->generateSlug($label);
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
     * reset component to its default state.
     *
     * @return void
     */
    private function resetState(): void
    {
        \array_walk_recursive(
            array: $this->state,
            callback: fn (&$val) => $val = \gettype($val) === "boolean" ? false : null
        );
    }

    /**
     * parse model instance's data into state.
     *
     * @param int $id
     * @return void
     */
    private function parseModelDataIntoState(int $id): void
    {
        $menu = NavMenu::findOrFail($id);

        $this->state['data']['label']    = $menu->label;
        $this->state['data']['slug']     = $menu->slug;
        $this->state['data']['type']     = $menu->type;
        $this->state['data']['sequence'] = $menu->sequence;
        $this->state['data']['id']       = $menu->id;
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
            'Are you sure deleting this menu? 👋',
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
    public function confirmed(): void
    {
        NavMenu::findOrFail($this->state['data']['id'])->delete();

        $this->resetPage(); // back to pagination first page

        $this->alert(
            'success',
            'Menu deleted successfully 👍'
        );
    }

    /**
     * cancel delete model instance listener.
     *
     * @return void
     */
    public function cancelled(): void
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
     * form validation rules.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'state.data.label'    => 'required|string',
            'state.data.sequence' => 'required|integer',
            'state.data.type'     =>
            [
                'required',
                Rule::in(['side', 'top']),
            ],
            'state.data.slug'     => [
                'required',
                Rule::unique('nav_menus', 'slug')->ignore($this->state['data']['id']),
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
            'state.data.label'    => 'label',
            'state.data.slug'     => 'slug',
            'state.data.sequence' => 'sequence',
            'state.data.type'     => 'type',
        ];
    }

    /**
     * get valid data.
     *
     * @return array
     */
    private function getValidData(): array
    {
        $valid = $this->validate();
        $data = $valid['state']['data'];

        return [
            "label"    => $data['label'],
            "slug"     => $data['slug'],
            "sequence" => $data['sequence'],
            "type"     => $data['type'],
        ];
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
     * update model menu instance.
     *
     * @param array $data
     * @param array $currentState
     * @return void
     */
    private function update(array $data, array $currentState): void
    {
        try {
            $menu = NavMenu::findOrFail($this->state['data']['id']);
            $menu->update($data);
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
     * store new menu instance.
     *
     * @param array $data
     * @param array $currentState
     * @return void
     */
    private function store(array $data, array $currentState): void
    {
        try {
            NavMenu::create($data);
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
     * handle UI effects and model menu instance.
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
     * render component presentational UI.
     *
     * @return View|Factory
     */
    public function render(): View|Factory
    {
        return view('livewire.nav-menus', [
            'navMenus' => NavMenu::paginate()
        ]);
    }
}
